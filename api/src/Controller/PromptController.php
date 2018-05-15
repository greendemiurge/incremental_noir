<?php

namespace App\Controller;

use App\Entity\ArcSegment;
use App\Entity\Element;
use App\Entity\ElementType;
use App\Entity\FirstLines;
use App\Entity\PromptWord;
use App\Entity\StoryLine;
use App\Entity\StoryThread;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PromptController extends AbstractController {

  // @TODO: Put in try/catch statements to handle exceptions.

  /**
   * Matches /api/v1/get-prompt exactly
   *
   * @Route("api/v1/get-prompt")
   * @Method({"GET"})
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getPrompt() {
    $story_thread = $this->getThread();
    $thread_id = $story_thread->getId();
    $story_line_objects = $story_thread->getStoryLines();
    $elements_objects = $story_thread->getElement();
    $line_count = count($story_line_objects);
    $target_number_of_lines = $story_thread->getTargetNumberOfLines();
    $arc_segment = $this->getThisArcSegment($line_count, $target_number_of_lines);

    $prompt_lines = [];
    foreach ($story_line_objects as $story_line_object) {
      $prompt_lines[] = $story_line_object->getLine();
    }
    $prompt_lines = array_slice($prompt_lines, -5, 5);

    $elements = [];
    foreach ($elements_objects as $elements_object) {
      $element_type = $elements_object->getElementType();

      $elements[$element_type->getName()][] = [
        'name' => $elements_object->getName(),
        'description' => $elements_object->getDescription(),
      ];
    }

    return $this->json([
      'threadId' => $thread_id,
      'threadLocator' => $this->getLocator($thread_id, $target_number_of_lines),
      'promptLines' => $prompt_lines,
      'promptWords' => $this->getPromptWords(),
      'elements' => $elements,
      'newElements' => $this->getNewElements(),
      'arcSegment' => [$arc_segment->getName() => $arc_segment->getDescription()],
      'mayActivateNewElement' => $this->getMayActivateNewElement($elements_objects, $arc_segment),
      'isNew' => $line_count < 2,
      'expires' => $story_thread->getLeaseExpiration(),
    ]);
  }

  /**
   * Matches /api/v1/post-prompt exactly
   *
   * @Route("api/v1/post-prompt")
   * @Method({"POST"})
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function postPrompt(Request $request, EntityManagerInterface $em) {
    $posted = json_decode($request->getContent(), true);
    $story_thread = $this->getThreadById($posted['threadId']);

    // This handles lease expiration. If the lease date does not match then
    // another has checked this out and we should notify of expiration.
    if (strtotime($posted['expires']) != $story_thread->getLeaseExpiration()->getTimestamp()) {
      return $this->json([
        'status' => 'expired',
        'postedTimestamp' => strtotime($posted['expires']),
        'databaseTimestamp' => $story_thread->getLeaseExpiration()->getTimestamp()
      ]);
    }

    if (!empty($posted['newElement']['name']) && !empty($posted['newElement']['description']) && $new_element = $posted['newElement']) {
      $element = new Element();
      $element_type = $this->getElementTypeByName($new_element['type']);

      $element->setStoryThread($story_thread);
      $element->setName($new_element['name']);
      $element->setDescription($new_element['description']);
      $element->setElementType($element_type);
      $em->persist($element);
    }

    $line = new StoryLine();
    $line->setStoryThread($story_thread);
    $line->setLine($posted['newLine']);
    $line->setAuthorName(!empty($posted['authorName']) ? $posted['authorName'] : NULL);
    $em->persist($line);

    // Check the story thread back in.
    $story_thread->setLeaseExpiration(NULL);
    $em->persist($story_thread);
    $em->flush();

    $locator = $this->getLocator($posted['threadId'], $story_thread->getTargetNumberOfLines());
    return $this->getThreadByLocator($locator);
  }

  /**
   * Matches /api/v1/cancel-prompt exactly
   *
   * Cancels the lease on a thread.
   *
   * @Route("api/v1/cancel-prompt")
   * @Method({"POST"})
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function cancelPrompt(Request $request, EntityManagerInterface $em) {
    $posted = json_decode($request->getContent(), true);
    $story_thread = $this->getThreadById($posted['threadId']);
    $story_thread->setLeaseExpiration(NULL);
    $em->persist($story_thread);
    $em->flush();

    return $this->json([
      'status' => 'success',
    ]);
  }

  /**
   * Matches /api/v1/get-thread-random exactly
   *
   * @Route("api/v1/get-thread-random")
   * @Method({"GET"})
   *
   */
  public function getThreadRandom() {
    $thread_id = $this->getDoctrine()
      ->getRepository(StoryThread::class)
      ->findRandomCompleteId();
    $story_thread = $this->getThreadById($thread_id);
    $locator = $this->getLocator($thread_id, $story_thread->getTargetNumberOfLines());

    return $this->getThreadByLocator($locator);
  }

  /**
   * Matches /api/v1/get-thread-by-locator/*
   *
   * @Route("api/v1/get-thread-by-locator/{locator}")
   * @Method({"GET"})
   *
   * @param string $locator
   *    The locator passed in.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   */
  public function getThreadByLocator(string $locator) {
    $thread_id = $this->decodeLocator($locator);
    $story_thread = $this->getThreadById($thread_id) ;
    $story_lines = $story_thread->getStoryLines();

    $lines = [];
    foreach ($story_lines as $story_line) {
      $lines[] = [
        'line' => $story_line->getLine(),
        'author' => $story_line->getAuthorName(),
      ];
    }

    return $this->json([
      'status' => 'success',
      'threadId' => $thread_id,
      'threadLocator' => $locator,
      'lines' => $lines,
      'isComplete' => $story_thread->getIsComplete(),
    ]);
  }

  /**
   * Computes a locator given the number of lines and the id.
   *
   * The number of lines is used to help keep the locator non-sequential despite
   * simply being a base 36 encoding. We concatenate the target number of lines
   * with a left-padded version of the thread identifier then convert the base
   * to include all numbers and letters. We then upper case transform it and
   * return the value.
   *
   * @param int $thread_id
   *   The PK identifier of the story thread.
   * @param int $target_number_of_lines
   *   The target number of lines, randomly chosen between 40 and 60 when the
   *   thread was created.
   *
   * @return string
   *   The locator string
   */
  protected function getLocator(int $thread_id, int $target_number_of_lines) {
    return strtoupper(base_convert((int) ($target_number_of_lines . str_pad( $thread_id, 5, '0', STR_PAD_LEFT )), 20, 36));
  }

  /**
   * This takes the generated locator string and returns the story thread id.
   *
   * @param string $locator
   *   The generated locator.
   *
   * @return int
   *   The PK identifier of the story thread.
   */
  protected function decodeLocator($locator) {
    return (int) substr(base_convert(strtolower($locator), 36, 20), 2);
  }

  /**
   * Checks to see if there is room in the story thread for a new element.
   *
   * There is a quota of new elements we permit by story arc. This checks to see
   * if we are under the quota and the user may submit a new element.
   *
   * @param object $elements_objects
   *   An object containing the elements on this thread already.
   *
   * @param \App\Entity\ArcSegment $arc_segment
   *   The Arc Segment object that describes the portion of the story arc the
   *   thread is currently in.
   *
   * @return bool
   *   TRUE if the user should be allowed to create a new element.
   */
  protected function getMayActivateNewElement(object $elements_objects, ArcSegment $arc_segment) {
    return count($elements_objects) <= $arc_segment->getMaxElements();
  }

  /**
   * Given a story thread, determine which portion of the story arc we are in.
   *
   * The database describes a schema for determining at what percentage of
   * completion we enter a new segment of the story arc. we take in the number
   * of lines the story currently has and the target number of lines at which
   * the story will be completed, and we match that against the schema to see
   * where we are in the story arc.
   *
   * @param $line_count
   *   The number of lines already present on the story thread.
   * @param $target_number_of_lines
   *   The number of lines at which the story will be considered completed.
   *
   * @return \App\Entity\ArcSegment|null|object
   *   The ArcSegment object describing our current location in the story.
   */
  protected function getThisArcSegment($line_count, $target_number_of_lines) {
    $expositon = $this->getArcSegmentByName('Exposition');
    $rising_action = $this->getArcSegmentByName('Rising Action');
    $turning_point = $this->getArcSegmentByName('Turning Point');
    $falling_action = $this->getArcSegmentByName('Falling Action');
    $conclusion = $this->getArcSegmentByName('Conclusion');
    $final_line = $this->getArcSegmentByName('Final Line');
    $percent_completed = ($line_count / $target_number_of_lines) * 100;

    switch (TRUE) {
      case ($line_count == 1): {
        return $expositon;
      }
      case ($line_count == ($target_number_of_lines - 1)): {
        return $final_line;
      }
      case ($percent_completed > $rising_action->getStartsAtPercentCompleted()): {
        return $rising_action;
      }
      case ($percent_completed > $turning_point->getStartsAtPercentCompleted()): {
        return $turning_point;
      }
      case ($percent_completed > $falling_action->getStartsAtPercentCompleted()): {
        return $falling_action;
      }
      case ($percent_completed > $conclusion->getStartsAtPercentCompleted()): {
        return $conclusion;
      }
      default: {
        return $expositon;
      }
    }
  }

  /**
   * Given the name of an arc segment, load its object.
   *
   * @param $name
   *   The human-readable name of the segment.
   *
   * @return \App\Entity\ArcSegment|null|object
   *   The ArcSegment object describing our current location in the story.
   */
  protected function getArcSegmentByName($name) {
    $segment_object = $this->getDoctrine()
      ->getRepository(ArcSegment::class)
      ->findOneBy(['name' => $name]);

    if (!$segment_object) {
      throw $this->createNotFoundException(
        'Segment not found'
      );
    }

    return $segment_object;
  }

  /**
   * Get an array of words, one of which must be used in the next line.
   *
   * @return array
   *   An array of words.
   */
    protected function getPromptWords() {
    $words = $this->getDoctrine()
      ->getRepository(PromptWord::class)
      ->findPromptWords();

    if (!$words) {
      throw $this->createNotFoundException(
        'Words not found'
      );
    }

    return $words;
  }

  /**
   * Get an element type object by name.
   *
   * @param string $name
   *   The name of the element type.
   *
   * @return \App\Entity\ElementType|null|object
   *   An Element type object.
   */
  protected function getElementTypeByName(string $name) {
    $element_type = $this->getDoctrine()
      ->getRepository(ElementType::class)
      ->findOneBy(['name' => $name]);

    if (!$element_type) {
      throw $this->createNotFoundException(
        'Element type not found'
      );
    }

    return $element_type;
  }

  /**
   * Get an array of possible element types to add to the story.
   *
   * @return array
   *    An array of Element type objects.
   */
  protected function getNewElements() {
    $new_element_objects  = $this->getDoctrine()
      ->getRepository(ElementType::class)
      ->findAll();

    if (!$new_element_objects) {
      throw $this->createNotFoundException(
        'New elements data not found'
      );
    }
    $new_elements = [];
    foreach ($new_element_objects as $new_element_object) {
      $new_elements[] = [
        'id' => $new_element_object->getId(),
        'name' => $new_element_object->getName(),
        'instructions' => $new_element_object->getInstructions(),
        'isUserSelectable' => $new_element_object->getIsUserSelectable(),
      ];
    }

    return $new_elements;
  }

  /**
   * Get a thread object for use.
   *
   * Check to see if there are available threads to check out. If so it will
   * check the thread out. If not it will spawn a new one and check it out.
   *
   * @return \App\Entity\StoryThread|null|object
   */
  protected function getThread() {
    $available = $this->getDoctrine()
      ->getRepository(StoryThread::class)
      ->findAvailableThreads();

    if (empty($available) || (count($available) < 3)) {
      return $this->spawnNewThread();
    }

    $random = rand(0, 3);
    $thread_id = $available[$random];

    return $this->checkOutThread($thread_id);
  }

  protected function getThreadById($thread_id) {
    $story_thread = $this->getDoctrine()
      ->getRepository(StoryThread::class)
      ->find($thread_id);

    if (!$story_thread) {
      throw $this->createNotFoundException(
        'Story thread not found'
      );
    }

    return $story_thread;
  }

  /**
   * Gets a thread by its id, checks it out, and returns it.
   *
   * @param int $thread_id
   *   The primary key identifier of the story thread.
   *
   * @return \App\Entity\StoryThread|null|object
   *   The story thread object.
   *
   * @throws \Exception
   *   If the story thread cannot be found.
   */
  protected function checkOutThread($thread_id) {
    $em = $this->getDoctrine()->getManager();
    $em->clear();
    $story_thread = $this->getThreadById($thread_id);

    $time = new \DateTime();
    // @TODO: Make this lease interval soft-configurable.
    $time->add(new \DateInterval('PT8H'));
    $story_thread->setLeaseExpiration($time);
    $em->persist($story_thread);
    $em->flush();

    return $story_thread;
  }

  /**
   * Creates a new thread and checks it out.
   *
   * @return \App\Entity\StoryThread
   *   The story thread object.
   *
   * @throws \Exception
   *   If it can't find a first line to use.
   */
  protected function spawnNewThread() {
    $em = $this->getDoctrine()->getManager();
    $story_thread = new StoryThread();

    $first_line_text = $this->getDoctrine()
      ->getRepository(FirstLines::class)
      ->findRandomFirstLine();

    if (!$first_line_text) {
      throw $this->createNotFoundException(
        'First line text not found.'
      );
    }

    $first_arc_segment = $this->getDoctrine()
      ->getRepository(ArcSegment::class)
      ->findOneBy(['name' => 'Exposition']);

    $story_thread->setCurrentArcSegment($first_arc_segment);
    $story_thread->setTargetNumberOfLines(rand(40, 60));
    $story_thread->setIsComplete(FALSE);
    $em->persist($story_thread);
    $em->flush();

    $first_line = new StoryLine();
    $first_line->setLine($first_line_text);
    $first_line->setStoryThread($story_thread);
    $em->persist($first_line);
    $em->persist($story_thread);
    $em->flush();

    return $this->checkOutThread($story_thread->getId());
  }

}

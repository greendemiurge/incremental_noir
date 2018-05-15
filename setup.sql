INSERT INTO element_type (name, instructions, is_user_selectable)
VALUES
  ('Protagonist', 'This is your main character. Give them a name and a description.', 0),
  ('Character', 'Add another character who will drive the plot along. This could be a client asking for help, a thug trying to intimidate the protagonist, a cop demanding answers. Give a name and description and they will appear on the notepad. Then use them in the story.', 1),
  ('Place', 'Add a place where action will occur. This could be the lobby of a hotel, an abandoned warehouse by the docks, a seedy nightclub. Give the place a name and description and it will appear on the notepad. Then use it in the story.', 1),
  ('Object', 'Add an object that is critical to the plot. The object could be the MacGuffin that drives the story along, the clue or evidence that leads the protagonist forward, the obstacle protagonist has to overcome. Give the object a name and a description and it will appear on the notepad. Then use it in the story.', 1);


INSERT INTO arc_segment (name, description, starts_at_percent_completed, max_elements)
VALUES
  ('Exposition', 'This story\'s just starting, feel free to add new elements to build intrigue.', 0, 3),
  ('Rising Action', 'Start building the tension. Make trouble and complication for the protagonist.', 10, 6),
  ('Turning Point', 'Surprise! Write a line that makes the whole story turn towards resolution.', 55, 7),
  ('Falling Action', 'Start tying up the loose ends.', 60, 8),
  ('Conclusion', 'Start giving the story a sense of closure.', 90, 8),
  ('Final Line', 'You get the last word! Write a final line to tie everything up.', 100, 8);

INSERT INTO first_lines (line)
VALUES
  ('I knew right away I should never have picked up that phone.'),
  ('It was a dark and stormy night.'),
  ('As I looked out over the bay I was filled with a sense of foreboding.'),
  ('I heard sharp, insistent knocking at my hotel room door, which was all the more unsettling because I hadn\'t told anyone where I was staying.'),
  ('I had told myself I would never take a case like this again, but I was always terrible at keeping promises, especially to myself.'),
  ('The whole place seemed to be laid out according to the chaotic logic of dreams.'),
  ('Throughout my life my only true guiding principle was to look out for my own good, but now even that principle was being challenged.'),
  ('What\'s the good of a reward if you don\'t live to spend it?');

import React from "react";
import isEmpty from "lodash/isEmpty";
import { HIGHLIGHT_COLOR } from "../config";

class Instructions extends React.Component {
  render() {
    if (isEmpty(this.props.arcSegment)) {
      return <div className="instructions-div"></div>;
    }

    const storyPart = Object.keys(this.props.arcSegment)[0];
    const newInstructions = "You are beginning a new story. The first line is provided, but the protagonist needs a name and a description. Supply those on the right and then add a second sentence to the story using at least one of the words on the left.";
    const continueInstructions = "Add a line to the portion of the story you can see below. You can't see all the story, just part of it, so write the next sentence using at least one of the words on the left. On the right is a notepad with the names of characters, places, and plot objects in this story.";
    const instructions = this.props.isNew ? newInstructions : continueInstructions;
    const addProtagonist = "You must name and describe the protagonist (on the right) before you can add a line";
    const addElement = "You may also add a new character, place or plot object to the story on the top right.";

    let newElementInstructions = this.props.isNew ? addProtagonist : (this.props.mayActivateNewElement ? addElement : "");

    return(
      <div className="instructions-div">
        <h2>Instructions</h2>
        <p>{instructions}</p>
        <h3 style={{color: HIGHLIGHT_COLOR}}>Current part of the plot: {storyPart}</h3>
        <p>{this.props.arcSegment[storyPart]}</p>
        <p>{newElementInstructions}</p>
      </div>
    )
  }
}

export default Instructions;
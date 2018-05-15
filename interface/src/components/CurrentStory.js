import React from "react";
import PromptLines from "./PromptLines";
import AuthorSubmissionForm from "./AuthorSubmissionForm";
import SentenceSubmissionForm from "./SentenceSubmissionForm";

class CurrentStory extends React.Component {
  render() {
    return(
      <div className="current-story-div">
        <PromptLines {...this.props} />
        <div className="typewriter">
          <div className="story-inputs">
            <SentenceSubmissionForm {...this.props} />
            <AuthorSubmissionForm {...this.props} />
          </div>
        </div>
      </div>
    )
  }
}

export default CurrentStory;
import React from "react";

class SentenceSubmissionForm extends React.Component {
  render() {
    return(
      <form className="sentence-form">
        <label>Next sentence:</label><br />
        <textarea 
          rows="2"
          className="sentence-form-input"
          onChange={(e) => this.props.onNewLineChange(e.target.value)}
        />
      </form>
    )
  }
}

export default SentenceSubmissionForm;
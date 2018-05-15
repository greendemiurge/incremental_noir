import React from "react";

class AuthorSubmissionForm extends React.Component {
  render() {
    return(
      <form className="add-author-form">
        <label>Author name (optional): </label>
        <input
          type="text"
          size="35"
          className="add-author-input"
          onInput={(e) => this.props.onAuthorChange(e.target.value)}
        />
      </form>
    )
  }
}

export default AuthorSubmissionForm;
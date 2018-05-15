import React from "react";
import isEmpty from "lodash/isEmpty";

class Errors extends React.Component {
  getErrors() {
    const errorMarkup = [];
    for (let error of this.props.errors) {
      errorMarkup.push(<li key={error}>{error}</li>);
    }

    return errorMarkup;
  }

  render() {
    if (isEmpty(this.props.errors)) {
      return null;
    }

    const visibility = this.props.submitMouseOver ? "visible" : "hidden";

    return(
      <div className="submit-error" style={{visibility}}>
        <h2>Before you can publish:</h2>
        <ul>
          {this.getErrors()}
        </ul>
      </div>
    )
  }
}

export default Errors;
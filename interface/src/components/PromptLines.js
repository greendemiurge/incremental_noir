import React from "react";
import isEmpty from "lodash/isEmpty";

class PromptLines extends React.Component {
  componentDidUpdate() {
    if (this.newLine) {
      this.newLine.scrollIntoView(false);
    }
  }

  getLines() {
    const {
      newLine,
      newLineArrayProcessed,
      promptLines
    } = this.props;

    if (isEmpty(promptLines)) {
      return [];
    }

    const lines = [];
    for (let line of promptLines) {
      lines.push(
        <span className="prompt-line" key={line}>{line}</span>
      );
    }

    if (newLine) {
      lines.push(
        <p
          className="new-prompt-line"
          key={newLine}
          ref={(el) => {this.newLine = el;}}
        >
        {newLineArrayProcessed}
      </p>
      );
    }

    return lines;
  }

  render() {
    return(
      <div className="prompt-lines">
        {this.getLines()}
      </div>
    )
  }
}

export default PromptLines;
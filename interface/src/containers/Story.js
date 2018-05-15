import React from "react";
import { HIGHLIGHT_COLOR } from "../config";

class Story extends React.Component {
  getLineMarkup() {
    const lineMarkup = [];
    for (let line of this.props.lines) {
      const isByHoverAuthor = (this.props.hoverAuthor && (this.props.hoverAuthor === line.author));
      lineMarkup.push(
        <span style={{backgroundColor: isByHoverAuthor ? HIGHLIGHT_COLOR : "inherit"}} key={line.author}>{line.line} </span>
      );
    }
    return lineMarkup;
  }

  getAuthorsMarkup() {
    const deDuplicationArray = [];
    const authorsMarkup = [];
    for (let line of this.props.lines) {
      if (line.author === null) {
        continue;
      }

      if (deDuplicationArray.includes(line.author)) {
        continue;
      }
      deDuplicationArray.push(line.author);

      authorsMarkup.push(
        <li
          onMouseOver={() => this.props.onMouseOverAuthor(line.author)}
          onMouseOut={() => this.props.onMouseOutAuthor(line.author)}
          key={line.author}
        >
          {line.author}&nbsp;
        </li>
      );
    }
    return authorsMarkup;
  }

  render() {
    return (
      <div className="story">
        <button
          className="return-home"
          onClick={() => this.props.onChangeViewMode("home")}
        >
          Return Home
        </button>
        <div className="story-locator" style={{color: HIGHLIGHT_COLOR}}>
          Locator: {this.props.threadLocator}
        </div>
        <div className="story-display">
          {this.getLineMarkup()}
        </div>
        <div className="story-authors-display">
          <h3>Authors:</h3>
          <ul>
            {this.getAuthorsMarkup()}
          </ul>
        </div>
      </div>
    );
  }
}

export default Story;
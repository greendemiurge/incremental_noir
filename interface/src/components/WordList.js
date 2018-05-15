import React from "react";

export class WordList extends React.Component {
   wordListBuilder() {
    const list = this.props.promptWords;
    let result = [];
    for (let i in list) {
      const word = list[i];
      const style = {textDecoration: "none"};
      if (this.props.usedWords.includes(word)) {
        style.textDecoration = "line-through";
      }

      result.push(
        <li className="word-list-li" key={i} style={style}>{word}</li>
      )
    }

    return result;
  }

  render() {
    return(
      <div className="word-list-div">
        <h2 className="word-list-label">Word List:</h2>
        <ul className="word-list-ul">
          {this.wordListBuilder()}
       </ul>
      </div>
    )
  }
}

export default WordList;
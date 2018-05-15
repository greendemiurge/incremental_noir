import React from "react";

class Selection extends React.Component {
  render() {
    return(
      <div className="selection-div">
        {this.props.type}
      </div>
    )
  }
}

export default Selection;
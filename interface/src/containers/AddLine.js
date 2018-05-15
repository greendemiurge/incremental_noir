import React from "react";
import WordList from "../components/WordList";
import CurrentStory from "../components/CurrentStory";
import Instructions from "../components/Instructions";
import AddNew from "../components/AddNew";
import PlotElements from "../components/PlotElements";
import Errors from "../components/Errors";

class AddLine extends React.Component {

  comp

  render() {
    return (
      <div className="add-line">
        <button
          className="return-home"
          onClick={() => this.props.onChangeViewMode("home")}
        >
          Return Home
        </button>
        <WordList {...this.props} />

        <Instructions {...this.props} />
        <AddNew {...this.props} />
        <PlotElements {...this.props} />
        <CurrentStory {...this.props} />
        <Errors {...this.props} />
        <button
          className="publish"
          onClick={this.props.submitNewLine}
          onMouseOver={this.props.onSubmitMouseover}
          onMouseOut={this.props.onSubmitMouseout}
        >
          Publish!
        </button>
      </div>
    );
  }
}

export default AddLine;
import React from "react";

class AddNewSubmissionForm extends React.Component {
  renderForm() {
    switch (this.props.type) {
      case "Character":
        return (
          <span className="help-text-span">
            Add a character to the story. This could be a burly hired goon, a shifty witness of a crime, or a skeptical cop. Give the character a name and a description and it will appear in the notepad. Then use it in the story.
          </span>
        );

      case "Place":
        return (
          <span className="help-text-span">
            Add a place where action will occur. This could be the lobby of a hotel, an abandoned warehouse by the docks, or a seedy nightclub. Give the place a name and a description and it will apear in the notepad, then use it in the story.
          </span>
        );

      case "Object":
        return (
          <span className="help-text-span">
            Add an object of interest to the story. This could be a suspected murder weapon, a hand-scribbled phone number, or an embossed cigarette case. Give the object a name and descrption and it will appear on the notepad. Then use it in the story.
          </span>
        );
      default:
        return null;
    }
  }

  render() {
    return(
      <div>
        <form>
          <label style={{fontSize: "16px"}} >Name:</label><br/>
          <input
            type="text"
            className="add-new-plot-element-input"
            onInput={(e) => this.props.onNewElementChange(e.target.value, "name")}
          />
          <br />
          <label style={{fontSize: "16px"}} >Description:</label><br/>
          <textarea
            rows="3"
            className="add-new-plot-element-input"
            onInput={(e) => this.props.onNewElementChange(e.target.value, "description")}
          />
          <br />
        </form>
        {this.renderForm()}
      </div>
    )
  }
}

export default AddNewSubmissionForm
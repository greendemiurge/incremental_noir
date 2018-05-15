import React from "react";
import { HIGHLIGHT_COLOR } from "../config";

class Home extends React.Component {
  render() {
    return (
      <div className="home">
        <div className="home-message">
          <h1 style={{marginBottom: 0, color: HIGHLIGHT_COLOR}}>Incremental Noir</h1>
          <p style={{marginTop: 0, fontStyle: "italic", fontSize: "90%"}}>An incremental writing game in the Noir genre.</p>
          <p>
            This is a collaborative writing exercise in which a story is
            contributed line by line by many different authors. No author gets
            to see the entire work. Instead each author can only see a few of
            the lines that came before. In order to make things interesting,
            each author has to incorporate at least one of three randomly
            generated words that are provided in a list.
          </p>
          <p>
            Periodically an author will be invited to add a story element (a
            character, place or object) that will be important to the plot.
            These will continue to show up on a list so that future authors will
            have them to reference.
          </p>
          <p>
            When you, make sure to take note of <span style={{color: HIGHLIGHT_COLOR}}>the story locator code</span> in the
            top left of the screen. You can use it to revisit the story later to
            see how it has progressed. You can also click the random button to
            look in on a random story whenever you like.
          </p>
        </div>
        <button className="home-add-line home-button" onClick={() => this.props.onChangeViewMode("addLine")}>
          Add Line
        </button>
        <button className="home-get-random home-button" onClick={() => this.props.onGoto("random")}>
          Read a random story
        </button>
        <button className="home-get-by-locator home-button" onClick={() => {
          if (this.props.locator) {
            this.props.onGoto(this.props.locator)
          }
        }
        }>
          Read a story by locator
        </button>
        <input
          type="text"
          onFocus={this.props.clearLocatorNotFound}
          className="home-get-by-locator-input"
          placeholder="Enter Locator Here"
          onInput={(e) => this.props.onLocatorChange(e.target.value)}
        />
        {this.props.locatorNotFound ? <span className="home-get-by-locator-error" style={{color: HIGHLIGHT_COLOR}}>Locator not found</span> : null}
      </div>
    );
  }
}

export default Home;
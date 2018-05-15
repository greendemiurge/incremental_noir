import React from "react";
import isEmpty from "lodash/isEmpty";

class PlotElements extends React.Component {
  partsBuilder() {
    const parts = {
      Protagonist: "Protagonist",
      Character: "Characters",
      Object: "Objects",
      Place: "Places",
    };
    const newElement = this.props.newElement;

    const PlotElementsJsx = [];
    for (let type in parts) {
      const part = !isEmpty(this.props.elements) ? this.props.elements[type] : [];

      let result = [];
      for (let i in part) {
        result.push(
          <p key={i}>{part[i].name}: {part[i].description}</p>
        )
      }

      if (newElement.type === type) {
        const divider = (newElement.name && newElement.description) ? ":" : "";
        result.push(
          <p className="new-element-span" key="new">{newElement.name}{divider} {newElement.description}</p>
        )
      }

      if (isEmpty(result)) {
        continue;
      }

      PlotElementsJsx.push(
        <div key={type}>
          <span className="story-part-label">{type}</span><br />
          {result}
        </div>
      );
    }

    return PlotElementsJsx;
  }

  render() {
    if (isEmpty(this.props.elements) && isEmpty(this.props.newElement)) {
      return <div className="plot-elements"><h2>Story Parts</h2></div>;
    }

    return(
      <div className="plot-elements">
        <h2>Plot Elements</h2>
        {this.partsBuilder()}
      </div>
    )
  }
}

export default PlotElements;
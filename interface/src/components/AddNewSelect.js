import React from "react";

class AddNewSelect extends React.Component {
  render() {
    const {newElements, onNewElementChange} = this.props;

    function optionBuilder() {
      let result = [];
      for (let i in newElements) {
        if (newElements[i].isUserSelectable === true) {
          result.push(
            <option value={newElements[i].name} key={i}>{newElements[i].name}</option>
          )
        }
      }

      return result;
    }

    if (this.props.isNew) {
      return <div className="add-new-select-div"><b>Protagonist</b></div>;
    }

    return(
      <div className="add-new-select-div">
        <select onChange={(e) => onNewElementChange(e.target.value, "type")}>
          {optionBuilder()}
        </select>
      </div>
    )
  }
}

export default AddNewSelect;
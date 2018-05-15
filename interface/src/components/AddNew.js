import React from "react";
import AddNewSelect from "./AddNewSelect";
import AddNewSubmissionForm from "./AddNewSubmissionForm";

class AddNew extends React.Component {
  constructor(props) {
    super(props);
    this.renderNewForm = this.renderNewForm.bind(this);
  }

  renderNewForm() {
    if (!this.props.type) {
      return null;
    }

    return (
      <AddNewSubmissionForm {...this.props} />
    )
  }

  render() {
    if (!this.props.mayActivateNewElement) {
      return <div className="add-new-div"></div>;
    }

    return(
      <div className="add-new-div">
        <h2>Add New</h2>
        <AddNewSelect {...this.props} />
        {this.renderNewForm()}
      </div>
    )
  }
}

export default AddNew;
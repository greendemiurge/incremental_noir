import React from "react";
import axios from "axios";
import get from "lodash/get";
import isEmpty from "lodash/isEmpty";
import { HIGHLIGHT_COLOR, HIGHLIGHT_BORDER} from "./config";
import AddLine from "./containers/AddLine";
import Home from "./containers/Home";
import Story from "./containers/Story";

import ReactGA from "react-ga";
if (process.env.REACT_APP_GOOGLE_ANALYTICS_UA) {
  ReactGA.initialize(process.env.REACT_APP_GOOGLE_ANALYTICS_UA);
}

const wordListError = "At least one word from the word list must be used.";
const defaultAddLineObject = {
  apiResponse: {},
  author: null,
  errors: [
    wordListError
  ],
  newElement: {
    type: "Character"
  },
  usedWords: [],
  newLineArrayProcessed: [],
  submitMouseOver: false,
};

const defaultStoryObject = {
  hoverAuthor: null,
  threadId: null,
  threadLocator: null,
  locatorNotFound: false,
  lines: [],
  isComplete: null,
};

class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      viewMode: "home",
      ...defaultAddLineObject,
      ...defaultStoryObject,
    };

    this.handleAuthorChange = this.handleAuthorChange.bind(this);
    this.handleGoto = this.handleGoto.bind(this);
    this.handleLocatorChange = this.handleLocatorChange.bind(this);
    this.handleMouseOverAuthor = this.handleMouseOverAuthor.bind(this);
    this.handleMouseOutAuthor = this.handleMouseOutAuthor.bind(this);
    this.handleNewElementChange = this.handleNewElementChange.bind(this);
    this.handleNewLineChange = this.handleNewLineChange.bind(this);
    this.handleViewModeChange = this.handleViewModeChange.bind(this);
    this.handleSubmitMouseover = this.handleSubmitMouseover.bind(this);
    this.handleSubmitMouseout = this.handleSubmitMouseout.bind(this);
    this.submitValidation = this.submitValidation.bind(this);
    this.submitNewLine = this.submitNewLine.bind(this);
  }

  viewModes = {
    home: Home,
    story: Story,
    addLine: AddLine
  };

  handleViewModeChange(viewMode) {
    if (process.env.REACT_APP_GOOGLE_ANALYTICS_UA) {
      ReactGA.pageview(viewMode);
    }

    this.setState({viewMode, apiResponse: {}});
    switch (viewMode) {
      case "addLine": {
        this.getPrompt();

        // After 4 hours, kick the user out and cancel the lease.
        setTimeout(this.handleViewModeChange, 14399000, "home");
        return;
      }

      default: {
        this.cancelPrompt();
        return;
      }
    }
  }

  handleNewElementChange(value, subfield) {
    this.setState((prevState, props) => {
      const newElement = {...prevState.newElement};
      newElement[subfield] = value;

      return {newElement}
    }, () => this.submitValidation());
  }

  handleLocatorChange(locator) {
    this.setState({locator});
  }

  handleNewLineChange(value) {
    const newLineArrayProcessed = this.processNewLine(value);
    this.setState({newLine: value, newLineArrayProcessed}, () => this.submitValidation());
  }

  handleAuthorChange(author) {
    this.setState({author});
  }

  handleSubmitMouseover() {
    this.setState({submitMouseOver: true});
  }

  handleSubmitMouseout() {
    this.setState({submitMouseOver: false});
  }

  handleGoto(locator) {
    let route = "/api/v1/get-thread-by-locator/" + locator;
    if (locator === "random") {
      route = "/api/v1/get-thread-random"
    }

    this.setState({locatorNotFound: false});

    console.log("GET: " + process.env.REACT_APP_END_POINT + "/api/v1/get-thread-by-locator/" + locator);

    axios.get(process.env.REACT_APP_END_POINT + route)
      .then((response) => {

        console.log("response", response.data);

        this.setState({
          viewMode: "story",
          threadId: response.data.threadId,
          threadLocator: response.data.threadLocator,
          lines: response.data.lines,
          isComplete: response.data.isComplete,
          ...defaultAddLineObject
        });
      })
      .catch((error) => {
      if (!isEmpty(error.response) && (error.response.status === 404))  {
        this.setState({locatorNotFound: true});
        return;
      }
        console.error(error);
      });
  }

  handleMouseOverAuthor(authorName) {
    this.setState({hoverAuthor: authorName});
  }

  handleMouseOutAuthor(authorName) {
    if (this.state.hoverAuthor === authorName) {
      this.setState({hoverAuthor: null});
    }
  }

  getPrompt() {
    console.log("GET: " + process.env.REACT_APP_END_POINT + "/api/v1/get-prompt");

    axios.get(process.env.REACT_APP_END_POINT + "/api/v1/get-prompt")
      .then((response) => {
        console.log("response", response.data);

        this.setState({apiResponse: response.data});

        // If this is a new thread, set the new element up for a protagonist.
        if (response.data.isNew) {
          const newElement = {type: "Protagonist"};
          this.setState({newElement, ...defaultStoryObject}, () => this.submitValidation());
        }
      })
      .catch((error) => {
        console.error(error);
      });
  }

  cancelPrompt() {
    const threadId = get(this.state, ["apiResponse", "threadId"], false);
    if (threadId === false) {
      return;
    }

    axios.post(process.env.REACT_APP_END_POINT + "/api/v1/cancel-prompt", {threadId})
      .then((response) => {
        return;
      })
      .catch((error) => {
        console.error(error);
      });
  }

  submitNewLine() {
    if (!isEmpty(this.state.errors)) {
      return;
    }

    const payload = {
      threadId: this.state.apiResponse.threadId,
      newLine: this.state.newLine,
      newElement: this.state.newElement,
      authorName: this.state.author,
      expires: this.state.apiResponse.expires
    };

    console.log("POST: " + process.env.REACT_APP_END_POINT +"/api/v1/post-prompt", payload);

    axios.post(process.env.REACT_APP_END_POINT + "/api/v1/post-prompt", payload)
      .then((response) => {
        console.log("response", response.data);

        this.setState({
          viewMode: "story",
          threadId: response.data.threadId,
          threadLocator: response.data.threadLocator,
          lines: response.data.lines,
          isComplete: response.data.isComplete,
          ...defaultAddLineObject
        });
      })
      .catch((error) => {
        console.error(error);
      });
  }

  processNewLine(newLine) {
    const promptWords = !isEmpty(this.state.apiResponse.promptWords) ? this.state.apiResponse.promptWords : [];
    let usedWords = [];

    let newLineArray = [newLine];
    for (let word of promptWords) {
      newLineArray = this.recursiveWordSearchNewLine(newLineArray, word);
    }

    const newLineArrayProcessed = [];
    for (let lineNumber in  newLineArray) {
      const line = newLineArray[lineNumber];
      const style = {backgroundColor: "inherit"};

      if (promptWords.includes(line)) {
        style.color = "#000";
        style.border = " 1px solid";
        style.backgroundColor = HIGHLIGHT_COLOR;
        style.borderColor = HIGHLIGHT_BORDER;
        if (!usedWords.includes(line)) {
          usedWords = this.state.usedWords.concat([line]);
        }
      }

      newLineArrayProcessed.push(<span style={style} key={lineNumber} >{line}</span>)
    }
    this.setState({usedWords});

    return newLineArrayProcessed;
  }

  recursiveWordSearchNewLine(newLineArray, word) {
    let newLineArrayOutput = [];

    for (let lineItem of newLineArray) {
      const tempArray = lineItem.split(word);

      // If there is at least one instance of the word,
      if (tempArray.length > 1) {
        let i = 1;
        while (i < tempArray.length) {
          tempArray.splice(i, 0, word);
          i += 2;
        }

        newLineArrayOutput = newLineArrayOutput.concat(tempArray);
        continue
      }

      newLineArrayOutput = newLineArrayOutput.concat([lineItem]);
    }

    return newLineArrayOutput;
  }

  submitValidation() {
    const errors = [];
    const elementName = get(this.state, ["newElement", "name"], false);
    const elementDescription = get(this.state, ["newElement", "description"], false);

    // If this is a new thread, a protagonist must be named and described.
    if (this.state.apiResponse.isNew && !elementName && !elementDescription) {
      errors.push("Since this is a new story you need to name and describe the protagonist.");
    }

    // If an element is named it must be described.
    if (elementName && !elementDescription) {
      errors.push("If an element has a name it must also have a description.");
    }

    // If an element is described it must be named.
    if (!elementName && elementDescription) {
      errors.push("If an element has a description it must also have a name.");
    }

    // At least one word must be used.
    if (isEmpty(this.state.usedWords)) {
      errors.push(wordListError);
    }

    this.setState({errors});
  }

  render() {
    const props = {
      ...this.state.apiResponse,
      type: this.state.newElement.type,
      errors: this.state.errors,
      newElement: this.state.newElement,
      newLine: this.state.newLine,
      newLineArrayProcessed: this.state.newLineArrayProcessed,
      locator: this.state.locator,
      onAuthorChange: this.handleAuthorChange,
      onGoto: this.handleGoto,
      onLocatorChange: this.handleLocatorChange,
      onNewElementChange: this.handleNewElementChange,
      onNewLineChange: this.handleNewLineChange,
      onChangeViewMode: this.handleViewModeChange,
      onSubmitMouseover: this.handleSubmitMouseover,
      onSubmitMouseout: this.handleSubmitMouseout,
      submitMouseOver: this.state.submitMouseOver,
      submitNewLine: this.submitNewLine,
      threadId: this.state.threadId,
      threadLocator: this.state.threadLocator,
      locatorNotFound: this.state.locatorNotFound,
      clearLocatorNotFound: () => this.setState({locatorNotFound: false}),
      lines: this.state.lines,
      onMouseOverAuthor: this.handleMouseOverAuthor,
      onMouseOutAuthor: this.handleMouseOutAuthor,
      hoverAuthor: this.state.hoverAuthor,
      isComplete: this.state.isComplete,
      usedWords: this.state.usedWords
    };

    const ViewMode = this.viewModes[this.state.viewMode];

    return (
      <div className="wrapper">
        <ViewMode {...props} />
      </div>
    );
  }
}

export default App;

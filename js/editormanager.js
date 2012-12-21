// Inner class for managing the Ace editors
function EditorManager () {
    
    // Initialize the ace editor object
    this.editor = ace.edit("editor");
    this.eSession = this.editor.getSession();
    this.eRenderer = this.editor.renderer;
    this.editor.setTheme("ace/theme/twilight");
    this.eSession.setMode("ace/mode/c_cpp");
    this.editor.setHighlightActiveLine(false);
    this.editor.setValue("Welcome to your project! Select a file to edit.");
    
    // Initialize the ace editor object
    this.terminal = ace.edit("terminal");
    this.tSession = this.terminal.getSession();
    this.tRenderer = this.terminal.renderer;
    this.terminal.setTheme("ace/theme/twilight");
    this.tSession.setMode("ace/mode/text");
    this.terminal.setReadOnly(true);
    this.terminal.setHighlightActiveLine(false);
    this.tRenderer.setShowGutter(false);
    this.tRenderer.setShowPrintMargin(false);
    this.terminal.setValue("");
    
    
    /* Custom key bindings */    
    this.editor.commands.addCommand({
        name: 'saveFile',
        bindKey: {win: 'Ctrl-S', mac: 'Command-S'},
        exec: function(editor) {
            saveFile();
        }
    });

    
    
    /* Custom Editor Functions */
    
    this.appendValue = function (newValue, aceEditor) {
        var oldValue = aceEditor.getValue();
        
        // For readability purposes - can be omitted if desired
        //oldValue += "\n";
        
        aceEditor.setValue(oldValue + newValue, 1);
    }
    
}

EM = new EditorManager();
console.log("Created the EditorManager object");
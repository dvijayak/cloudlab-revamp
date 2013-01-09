// Inner class for managing the Ace editors
function EditorManager () {    
    // Initialize the ace editor object
    this.editor = ace.edit("editor");
    this.eSession = this.editor.getSession();
    this.eRenderer = this.editor.renderer;
    this.editor.setTheme("ace/theme/twilight");
    this.eSession.setMode("ace/mode/c_cpp");
    //this.editor.setReadOnly(true);
    this.editor.setHighlightActiveLine(false);
    this.eSession.setUseWrapMode(true);
    this.editor.setValue("Welcome to your project! Select a file to edit.");    
    
    // Initialize the ace editor object
    this.terminal = ace.edit("terminal");
    this.tSession = this.terminal.getSession();
    this.tRenderer = this.terminal.renderer;
    this.terminal.setTheme("ace/theme/cobalt");
    this.tSession.setMode("ace/mode/text");
    this.terminal.setReadOnly(true);
    this.terminal.setHighlightActiveLine(false);
    this.tSession.setUseWrapMode(true);
    //this.tRenderer.setShowGutter(false);
    this.tRenderer.setShowPrintMargin(false);    
    
    
    /* Custom key bindings */
    this.editor.commands.addCommand({
        name: 'newFile',
        bindKey: {win: 'Ctrl-Alt-N', mac: 'Command-Option-N'},
        exec: function(editor) {            
            $( "#newFileDialog").modal({keyboard:true});
        }
    });
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
    };
    
    this.setPreference = function (preference, value, aceEditor) {        
        var editor = (aceEditor) ? aceEditor : {object: EM.editor, id: "editor"};
        switch (preference) {
            case 'theme':
                editor.object.setTheme("ace/theme/" + value);
                break;
            case 'fontsize':
                $( document ).ready(function () {
                    $( "#" + editor.id ).css('font-size', value + "px"); // assuming font sizes in pixels
                })                
                break;
            case 'tabwidth':
                editor.object.getSession().setTabSize(parseInt(value));
                break;
            case 'softtabs':
                editor.object.getSession().setUseSoftTabs(value);
                break;
            case 'showlinenos':
                editor.object.renderer.setShowGutter(value);
                break;
            case 'activeline':
                editor.object.setHighlightActiveLine(value);
                break;
            case 'printmargin':
                editor.object.setShowPrintMargin(value);
                break;
            case 'wordwrap':
                editor.object.getSession().setUseWrapMode(value);
                break;
        }
    };            
}

//$( document ).ready(function () {
    EM = new EditorManager();
    console.log("Created the EditorManager object");
//})
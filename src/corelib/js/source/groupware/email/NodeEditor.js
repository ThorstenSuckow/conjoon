/**
 * conjoon
 * (c) 2002-2009 siteartwork.de/conjoon.org
 * licensing@conjoon.org
 *
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('de.intrabuild.groupware.email');


de.intrabuild.groupware.email.NodeEditor = function(treePanel, config) {

    Ext.apply(this, config);

    /**
     * @ext-bug beta1 cancelOnEsc is ignored in the onSpecialKey implementation
     *                of the TreeEditor, make it custom
     */
    this.addEvents({
        'escapekey'    : true,
        'enterkey'     : true
    });


    /**
     * @ext-bug beta1 Applying these values in the config gets ignored
     */
    this.shadow        = 'drop';
    this.revertInvalid = false;


    /**
     * The editor for the tree's nodes.
     * The beforeNodeClick method gets assigned to an empty function
     * so cliking on a node won'
     */
    de.intrabuild.groupware.email.NodeEditor.superclass.constructor.call(this, treePanel, {
        shadow        : false,
        validator     : this.isNodeNameValid.createDelegate(this),
        allowBlank    : false,
        revertInvalid : false,
        cancelOnEsc   : false
    });

    // should prevent a selected node from being edited when
    // click on selected node occurs
    this.beforeNodeClick = Ext.emptyFn;


    // install listeners
    // looks like we can skip the call to onbeforecomplete, since first the
    // beforehide will be called
    this.on('beforecomplete', this.onBeforeEditingComplete, this);
    this.on('escapekey',      this.onEditorEscape, this);
    this.on('enterkey',       this.onEditorEnter, this);
    this.on('complete',       this.onEditorComplete, this);
    this.on('show',           this.onEditorShow,  this);
    this.on('beforehide',     this.onBeforeHide,  this);
    this.on('hide',           this.onEditorHide,  this);

};


Ext.extend(de.intrabuild.groupware.email.NodeEditor, Ext.tree.TreeEditor, {

// ------------------------- Members -------------------------------------------
    /**
     * Edit modes
     */
    NONE : 0,
    EDIT : 1,
    SAVE : 2,

    /**
     * The current mode the editor is in. Equals to SAVE, EDIT or NONE
     */
    editMode : this.NONE,


// ----------------------------- Methods ---------------------------------------

    initEditor : function(tree)
    {
        de.intrabuild.groupware.email.NodeEditor.superclass.initEditor.call(this, tree);
        // needed to overwrite since the mousdwon wad not in the ext2.0rc1
        // this is needed since a node which is currently being edited must not be draggable,
        // but without this custom implementation the tree would allow a node being
        // currently edited to be dragged around
        tree.getTreeEl().on('mousedown', this.hide, this);
    },

    /**
     * Validator for the node editor.
     */
    isNodeNameValid : function(value)
    {
        if (!this.editNode) {
            return true;
        }

        var value = value.trim();

        if (value == "") {
            return false;
        }

        // editnode still available?
        if (!this.editNode.parentNode) {
            return true;
        }

        return this.tree.isNodeNameAvailable(this.editNode.parentNode, this.editNode, value);
    },

    /**
     * Returns <tt>true</tt> if a node is being edited and the editor
     * waits for valid data.
     */
    isEditPending : function()
    {
        return (this.editNode != null && !this.field.isValid());
    },

    /**
     *
     */
    triggerEdit : function(node, mode)
    {
        this.editMode = mode;
        /**
         * @ext-bug2.2
         * see http://www.extjs.com/forum/showthread.php?t=41432
         */
        var as = this.tree.autoScroll;
        this.tree.autoScroll = false;
        de.intrabuild.groupware.email.NodeEditor.superclass.triggerEdit.call(this, node);
        this.tree.autoScroll = as;
    },

    /**
     *
     */
    resetEdit : function(value, startValue)
    {
        this.field.setValue(startValue);
        this.field.selectText();
        this.field.focus();
        this.isWarning = false;
    },

    // private
    updateNode : function(ed, value)
    {
        de.intrabuild.groupware.email.NodeEditor.superclass.updateNode.call(this, ed, value.trim());
    },

// ------------------------------ Listeners ------------------------------------
    /**
     * Method occurs no matter what. Tree calls it. Check if editor exists and
     * react.
     */
    onBeforeHide : function()
    {
        if (this.isWarning || this.isEditPending()) {
            return false;
        }

        return true;
    },

    /**
     * Checks wether the node name is valid and starts a XMLHttpRequest to save
     * the changes.
     *
     * @param {Ext.tree.TreeEditor}
     * @param mixed value
     * @param mixed startValue
     */
    isWarning : false,
    onBeforeEditingComplete : function(editor, value, startValue)
    {
        var msg = Ext.MessageBox;

        if (this.isWarning) {
            return false;
        }

        if (!this.field.isValid()) {
            this.isWarning = true;
            this.tree.valueInvalid(editor, value, startValue);
            return false;
        }

        return true;
    },

    /**
     * Starts saving the newly added/edited node respecting SAVE or EDIT mode.
     * The complete event gets fired even when a node was edited and it's value
     * did not change when requesting another components focus using mouse/keyboard.
     * However, if we are in EDIT mode, comparing the actual value with the start
     * value will tell us if we should proceed in requesting a XMLHttpRequest for
     * this. In SAVE mode the node was newly added, thus requesting a XMLHttpRequest
     * object is needed in every case.
     *
     *
     */
    onEditorComplete : function(editor, value, startValue)
    {
        if (this.editMode == this.NONE ||
           (this.editMode == this.EDIT && value.trim() === startValue.trim())) {
            return;
        }

        this.tree.saveNode({
            mode   : (this.editMode == this.EDIT ? 'edit' : 'add'),
            parent : this.editNode.parentNode.id,
            child  : {
                id         : this.editNode.id,
                value      : value,
                startValue : startValue
            }
        });

        this.editMode  = this.NONE;
        this.isWarning = false;
    },

    /**
    *
    */
    onEditorShow : function()
    {
        //alert(this.editMode+": SHOW");
    },

    /**
    *
    */
    onEditorHide : function()
    {
        //alert(this.editMode+": HIDE");
    },

    /**
     *
     */
    onEditorEnter : function(field, e)
    {
         e.stopEvent();
         this.completeEdit();
    },

    /**
     * When the user presses escapes he usually wants to revert any changes and
     * does not go into save mode. However, we hate to check if the node gets
     * edited or if it is a new node that needs to get saved.
     * In edit mode, we simply revert the changes and hide the editor, not invoking
     * any event.
     * In save mode however, we invoke the completeEdit method after we reverted
     * the fields value.
     *
     *
     */
    onEditorEscape : function(field, e)
    {
        switch (this.editMode) {
            case this.SAVE:
                e.stopEvent();
                this.field.setValue(this.startValue);
                this.completeEdit();
            return;
            case this.EDIT:
                e.stopEvent();
                this.suspendEvents();
                this.cancelEdit();
                this.resumeEvents();
            return;
        }
    },

    /**
     *
     */
    onSpecialKey : function(field, e)
    {
        if (e.getKey() == e.ENTER && !e.hasModifier()) {
            this.fireEvent('enterkey', field, e);
        } else if (e.getKey() == e.ESC) {
            this.fireEvent('escapekey', field, e);
        }
    }

});

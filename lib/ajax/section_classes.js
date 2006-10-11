/*
 * library for ajaxcourse formats, the classes and related functions for sections and resources
 * this library requires a 'main' object created in calling document 
 *
 * $Id$
 *
 */


function section_class(id,group,config,isDraggable) {
    this.init_section(id,group,config,isDraggable);
}
YAHOO.extend(section_class, YAHOO.util.DDProxy);


section_class.prototype.debug = true;


section_class.prototype.init_section = function(id, group,config,isDraggable) {
    if (!id) { return; }	// id would be the html id attribute.

    this.is = 'section';
    this.sectionId = null;	// Section number. This is NOT the section id from
                            // the database.

    if (!isDraggable) {
        this.initTarget(id,group,config);
        this.removeFromGroup('sections');
    } else {                 
        this.init(id,group,config);         
        this.handle = null;           
    }

    this.createFrame();
    this.isTarget = true;

    this.resources = [];
    this.numberDisplay = null;	// Used to display the section number on the top left
                                // of the section. Not used in all course formats.
    this.summary = null;
    this.content_td = null;
    this.hidden = false;
    this.highlighted = false;
    this.showOnly = false;
    this.resources_ul = null;
    this.process_section();

    this.viewButton = null;
    this.highlightButton = null;
    this.showOnlyButton = null;
    this.init_buttons();

    if (isDraggable)this.add_handle();                       

    if (this.debug)YAHOO.log("init_section "+id+" draggable="+isDraggable);


    if (YAHOO.util.Dom.hasClass(this.getEl(),'hidden'))
        this.toggle_hide(null,null,true);

}

section_class.prototype.init_buttons = function() {      
    var commandContainer = this.getEl().childNodes[2];

    //clear all but show only button 
    var commandContainerCount = commandContainer.childNodes.length;
    for (var i=(commandContainerCount-1);i>0;i--) {
        commandContainer.removeChild(commandContainer.childNodes[i])
    }

    if (!this.isWeekFormat) {        
        var highlightbutton = main.mk_button('div','/pix/i/marker.gif');
        YAHOO.util.Event.addListener(highlightbutton,'click',this.mk_marker,this,true);
        commandContainer.appendChild(highlightbutton); 
        this.highlightButton = highlightbutton;            
    }
    var viewbutton = main.mk_button('div','/pix/i/hide.gif');
    YAHOO.util.Event.addListener(viewbutton,'click',this.toggle_hide,this,true);
    commandContainer.appendChild(viewbutton);
    this.viewButton = viewbutton;
}


section_class.prototype.add_handle = function() {
    var handleRef = main.mk_button('div','/pix/i/move_2d.gif',[['style','cursor:move']]);
    YAHOO.util.Dom.generateId(handleRef,'sectionHandle');

    this.handle = handleRef;

    this.getEl().childNodes[0].appendChild(handleRef);
    this.setHandleElId(this.handle.id);
}


section_class.prototype.process_section = function() {
    this.content_td = this.getEl().childNodes[1];

    if (YAHOO.util.Dom.hasClass(this.getEl(),'current')) {
        this.highlighted = true;
        main.marker = this;
    }

    //create holder for display number for access later

    this.numberDisplay = document.createElement('div');
    this.numberDisplay.innerHTML = this.getEl().childNodes[0].innerHTML;
    this.getEl().childNodes[0].innerHTML = '';
    this.getEl().childNodes[0].appendChild(this.numberDisplay);  

	this.sectionId = this.id.replace(/section-/i, '');	// Okay, we will have to change this if we
	                                                    // ever change the id attributes format
														// for the sections.

    if (this.debug)YAHOO.log("Creating section "+this.getEl().id+" in position "+this.sectionId);
    //find/edit resources

    this.resources_ul = this.content_td.getElementsByTagName('ul')[0];
    if (this.resources_ul == null) {
        this.resources_ul = document.createElement('ul');
        this.resources_ul.className='section';
        this.content_td.insertBefore(this.resources_ul, this.content_td.childNodes[2]);
    }

    var resource_count = this.resources_ul.getElementsByTagName('li').length;

    for (var i=0;i<resource_count;i++) {
        var resource = this.resources_ul.getElementsByTagName('li')[i];
        if (YAHOO.util.Dom.hasClass(resource,'resource')) {
            this.resources[this.resources.length] = new resource_class(resource.id,'resources',null,this);
            if (this.debug)YAHOO.log("Found resource");
        } else {
            this.resources[this.resources.length] = new activity_class(resource.id,'resources',null,this);
        }
    }                

    this.summary = YAHOO.util.Dom.getElementsByClassName('summary',null,this.getEl())[0].firstChild.data || '';
}

section_class.prototype.startDrag = function(x, y) {   
    //operates in point mode
    YAHOO.util.DDM.mode = YAHOO.util.DDM.POINT;

    //remove from resources group temporarily
    this.removeFromGroup('resources');

    //reinitialize dd element
    this.getDragEl().innerHTML = '';

    var targets = YAHOO.util.DDM.getRelated(this, true);
    if (this.debug)YAHOO.log(this.sectionId + " startDrag "+targets.length + " targets");
}

section_class.prototype.onDragDrop = function(e, id) {
    // get the drag and drop object that was targeted
    var target = YAHOO.util.DDM.getDDById(id);

    if (this.debug)YAHOO.log("Section dropped on id="+id+" el = "+this.getEl().id+" x="+YAHOO.util.Dom.getXY(this.getDragEl()));

    this.move_to_section(target);

    //add back to resources group
    this.addToGroup('resources');
}  
section_class.prototype.endDrag = function() {
    //nessicary to defeat default action

    //add back to resources group
    this.addToGroup('resources');
}    

section_class.prototype.move_to_section = function(target) {
    var tempTd = document.createElement('td');
    var tempStore = null;
    var sectionCount = main.sections.length;
    var found = null;

    //determine if original is above or below target and adjust loop
    var oIndex=main.get_section_index(this);
    var tIndex=main.get_section_index(target);

    if (this.debug)YAHOO.log("original is at: "+oIndex+" target is at:"+tIndex+" of "+(sectionCount-1));

    if (oIndex < tIndex) {
        var loopCondition = 'i<sectionCount';
        var loopStart = 1;
        var loopInc = 'i++';
        var loopmodifier = 'i-1'; 
    } else {
        var loopCondition = 'i>0';
        var loopStart = sectionCount-1;  
        var loopInc = 'i--'; 
        var loopmodifier = 'i+1';       
    }

    //move on backend
    main.connect('post','class=section&field=move',null,'id='+this.sectionId+'&value='+(target.sectionId-this.sectionId));

    //move on front end
    for (var i=loopStart; eval(loopCondition); eval(loopInc)) {

        if ((main.sections[i] == this)&& !found) {
            //enounter with original node
            if (this.debug)YAHOO.log("Found Original "+main.sections[i].getEl().id);
            if (main.sections[i] == this) {
                found = true;
            }

        } else if (main.sections[i] == target) {
            //encounter with target node
            if (this.debug)YAHOO.log("Found target "+main.sections[i].getEl().id);
            main.sections[i].swap_with_section(main.sections[eval(loopmodifier)]);
            found = false;
            break;

        } else if (found) {
            //encounter with nodes inbetween
            main.sections[i].swap_with_section(main.sections[eval(loopmodifier)]);
        }
    }
}


section_class.prototype.swap_with_section = function(sectionIn) {    
    var tmpStore = null;

    thisIndex = main.get_section_index(this);
    targetIndex = main.get_section_index(sectionIn);
    main.sections[targetIndex] = this;
    main.sections[thisIndex] = sectionIn;

    this.changeId(targetIndex);
    sectionIn.changeId(thisIndex);

    if (this.debug)YAHOO.log("Swapping "+this.getEl().id+" with "+sectionIn.getEl().id);

    YAHOO.util.DDM.swapNode(this.getEl(), sectionIn.getEl());
}

section_class.prototype.toggle_hide = function(e,target,superficial) {
    if (this.hidden) {  
        YAHOO.util.Dom.removeClass(this.getEl(),'hidden');
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/show.gif/i,'hide.gif');          
        this.hidden = false;

        if (!superficial) {
            main.connect('post','class=section&field=visible',null,'value=1&id='+this.sectionId);
            for (var x=0;x<this.resources.length;x++) {                                
                this.resources[x].toggle_hide(null,null,true,this.resources[x].hiddenStored);
                this.resources[x].hiddenStored = null;
            }
        }

    } else {
        YAHOO.util.Dom.addClass(this.getEl(),'hidden');
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/hide.gif/i,'show.gif');           
        this.hidden = true;

        if (!superficial) {
            main.connect('post','class=section&field=visible',null,'value=0&id='+this.sectionId);
            for (var x=0;x<this.resources.length;x++) {
                this.resources[x].hiddenStored = this.resources[x].hidden;                                
                this.resources[x].toggle_hide(null,null,true,true);
            }             
        }
    }
}

section_class.prototype.toggle_highlight = function() {
    if (this.highlighted) {  
        YAHOO.util.Dom.removeClass(this.getEl(),'current');
        this.highlighted = false;
    } else {
        YAHOO.util.Dom.addClass(this.getEl(),'current');
        this.highlighted = true;
    }
} 

section_class.prototype.mk_marker = function() {
    if (main.marker != this) {
        main.update_marker(this);

    } else {//if currently the marker
        main.marker = null;

        main.connect('post','class=course&field=marker',null,'value=0');
        this.toggle_highlight();

    }
}    

section_class.prototype.changeId = function(newId) {                       
    this.sectionId = newId;       
    this.numberDisplay.firstChild.data = newId;                      

    //main.connectQueue_add('post','class=section&field=all',null,'id='+newId+"&summary="+main.mk_safe_for_transport(this.summary)+"&sequence="+this.write_sequence_list(true)+'&visible='+(this.hidden?0:1))           

    if (main.marker == this) {
        main.update_marker(this);                   
    }
}     

section_class.prototype.get_resource_index = function(el) {
    for (var x=0;x<this.resources.length;x++)
        if (this.resources[x]==el)
            return x;
    YAHOO.log("Could not find resource to remove "+el.getEl().id,"error");
    return -1;
}

section_class.prototype.remove_resource = function(el) {
    var resourceCount = this.resources.length;
    if (resourceCount == 1) {
        if (this.resources[0] == el)
            this.resources = new Array();    
    } else {
        var found = false;
        for (var i=0;i<resourceCount;i++) {
            if (found) {
                this.resources[i-1] = this.resources[i];         
                if (i==resourceCount-1) {
                    this.resources = this.resources.slice(0,-1);
                    resourceCount--;
                }
                this.resources[i-1].update_index(i-1);
            } else if (this.resources[i]==el) {
                found = true;
            }
        }
    }


    //remove "text" nodes to keep DOM clean
    var childIndex = null;
    var childrenCount = this.resources_ul.childNodes.length; 
    for (var i=0;i<childrenCount;i++)
        if (this.resources_ul.childNodes[i] == el.getEl())
            childIndex = i;
    if (childIndex > 0 && childIndex < this.resources_ul.childNodes.length)
        this.resources_ul.removeChild(this.resources_ul.childNodes[childIndex-1]);
    YAHOO.log("removing "+el.getEl().id);
    if (el.getEl().parentNode != null)
        el.getEl().parentNode.removeChild(el.getEl()); 

    this.write_sequence_list();          

}   

section_class.prototype.insert_resource = function(el, targetel) {
    var resourcecount = this.resources.length;
    var found = false;
    var tempStore = nextStore = null;

    //update in backend
	targetId = '';
	if (targetel != null) {
		targetId = targetel.id;
	}

	main.connect('post', 'class=resource&field=move', null,
			'id='+el.id+'&beforeId='+targetId
			+'&sectionId='+this.sectionId);

    //if inserting into a hidden resource hide
    if (this.hidden) {
        el.hiddenStored = el.hidden; 
        el.toggle_hide(null,null,true,true);           
    } else {
        if (el.hiddenStored != null) {
            el.toggle_hide(null,null,true,el.hiddenStored);
            el.hiddenStored = null;                
        }
    }

    //update model
    if (targetel == null) {
        this.resources[this.resources.length] = el;
    } else {
        for (var i=0;i<resourcecount;i++) {
            if (found) {
                tempStore = this.resources[i];
                this.resources[i] = nextStore;
                nextStore = tempStore;

                if (nextStore != null)     
                    nextStore.update_index(i+1); 

            } else if (this.resources[i] == targetel) {
                found = true;
                nextStore = this.resources[i];
                this.resources[i] = el;               
                resourcecount++;

                this.resources[i].update_index(i,this.ident);
                nextStore.update_index(i+1); 
            }                              
        }
	}
	
    //update on frontend 
    if (targetel != null) { 
        this.resources_ul.insertBefore(el.getEl(),targetel.getEl());
        this.resources_ul.insertBefore(document.createTextNode(''),targetel.getEl());
    } else {
        this.resources_ul.appendChild(el.getEl());
        this.resources_ul.appendChild(document.createTextNode(" "));
    }    
    el.parentObj = this;       
}    

section_class.prototype.write_sequence_list = function(toReturn) {
    var listOutput = '';
    for (var i=0;i<this.resources.length;i++) {
        listOutput += this.resources[i].id;
        if (i != (this.resources.length-1))
            listOutput += ',';
    }
    if (toReturn) {
        return listOutput;
	}
}   



/*
 * Resource Class extends util.DDProxy
 */   


function resource_class(id,group,config,parentObj) {

    this.init_resource(id,group,config,parentObj);
}
YAHOO.extend(resource_class, YAHOO.util.DDProxy);

resource_class.prototype.debug = true;

resource_class.prototype.init_resource = function(id,group,config,parentObj) {
    if (!id) { 
        YAHOO.log("Init resource, NO ID FOUND!",'error');
        return; 
    }

    this.is = 'resource';  
    this.init(id,group,config);  
    this.createFrame();     
    this.isTarget = true;

    this.id = this.getEl().id.replace(/module-/i,'');          

    this.hidden = false;
    if (YAHOO.util.Dom.hasClass(this.getEl().getElementsByTagName('a')[0],'dimmed'))
        this.hidden = true;
    this.hiddenStored = null;

    this.linkContainer = this.getEl().getElementsByTagName('a')[0];

    this.commandContainer = null;
    this.viewButton = null;
    this.handle = null;        
    this.init_buttons();

    this.parentObj = parentObj;        

    if (this.debug)YAHOO.log("init_resource "+id+" parent = "+parentObj.getEl().id);

}

resource_class.prototype.init_buttons = function() {
    var  commandContainer = YAHOO.util.Dom.getElementsByClassName('commands','span',this.getEl())[0];
    if ( commandContainer == null) {
        YAHOO.log('Cannot find command container for '+this.getEl().id,'error');
        return;
    }

    this.commandContainer = commandContainer;

    //find edit button
    var updateButton = null;
    var buttons =  commandContainer.getElementsByTagName('a');
    for (var x=0;x<buttons.length;x++) {
        if (buttons[x].title == main.portal.strings['update']) {
            updateButton = buttons[x];
        }
    }

    if (updateButton == null)
        YAHOO.log('Cannot find updateButton for '+this.getEl().id,'error');                     

    commandContainer.innerHTML = '';


    //add move-handle
    var handleRef = main.mk_button('a','/pix/i/move_2d.gif',[['style','cursor:move']],[['height','11'],['width','11'],['hspace','2'],['border','0']]);
    YAHOO.util.Dom.generateId(handleRef,'sectionHandle');
    this.handle = handleRef;

    commandContainer.appendChild(handleRef);
    this.setHandleElId(this.handle.id);



    //add edit button back in
    commandContainer.appendChild(updateButton);                  

    //add rest
    var button = main.mk_button('a','/pix/t/delete.gif');      
    YAHOO.util.Event.addListener(button,'click',this.delete_button,this,true);
    commandContainer.appendChild(button);       

    if (this.hidden)
        var button = main.mk_button('a','/pix/t/show.gif');      
    else
        var button = main.mk_button('a','/pix/t/hide.gif');                 
    YAHOO.util.Event.addListener(button,'click',this.toggle_hide,this,true);
    commandContainer.appendChild(button);   
    this.viewButton = button;

}    

resource_class.prototype.toggle_hide = function(target,e,superficial,force) {
    if (force != null) {
        if (this.debug)YAHOO.log("Resource "+this.getEl().id+" forced to "+force);
        this.hidden = !force;           
    }

    if (this.hidden) {
        YAHOO.util.Dom.removeClass(this.linkContainer,'dimmed');
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/show.gif/i,'hide.gif');
        this.hidden = false;  

        if (!superficial) {
            main.connect('post','class=resource&field=visible',null,'value=1&id='+this.id);
        }
    } else {
        YAHOO.util.Dom.addClass(this.linkContainer,'dimmed');
        this.viewButton.childNodes[0].src = this.viewButton.childNodes[0].src.replace(/hide.gif/i,'show.gif');
        this.hidden = true;

        if (!superficial) {
            main.connect('post','class=resource&field=visible',null,'value=0&id='+this.id);           
        }
    }
}

resource_class.prototype.delete_button = function() {
    if (this.debug)YAHOO.log("Deleting "+this.getEl().id+"from parent "+this.parentObj.getEl().id);

    if (!confirm(main.getString('deletecheck',main.getString(this.is)+" "+this.id))) {
        return false;
    }

    this.getEl().parentNode.removeChild(this.getEl());
    this.parentObj.remove_resource(this);

    main.connect('delete','class=resource&id='+this.id);        
}  

resource_class.prototype.update_index = function(index) {
    if (this.debug)YAHOO.log("update Index for resource "+this.getEl().id+"to"+index);
}   


resource_class.prototype.startDrag = function(x, y) {   
    //operates in intersect mode
    YAHOO.util.DDM.mode = YAHOO.util.DDM.INTERSECT;

    //reinitialize dd element
    this.getDragEl().innerHTML = '';

    var targets = YAHOO.util.DDM.getRelated(this, true);
    if (this.debug)YAHOO.log(this.id + " startDrag "+targets.length + " targets");

}

resource_class.prototype.onDragDrop = function(e, ids) {
    // best fit Id
    var id=[];

    for (var i=0; i<ids.length; i++) {
        if (ids[i].is == 'resource') {
            id[id.length] = ids[i];
		}
	}
    if (id.length == 0) {
        id = ids;
	}

    // get the drag and drop object that was targeted
    var target = YAHOO.util.DDM.getBestMatch(id);

    if (this.debug) {
		YAHOO.log("Dropped on section id="+target.sectionId
						+", el="+this.getEl().id
						+", x="+YAHOO.util.Dom.getXY( this.getDragEl() ));
	}
/*	var oldid = this.parentObj.id;
	this.previousId = oldid.replace(/section-/i, '');*/
    this.parentObj.remove_resource(this);

    if (target.is == 'resource' || target.is == 'activity') {
        target.parentObj.insert_resource(this, target);
    } else if (target.is == 'section') {
        target.insert_resource(this);
    }
    return;
}

resource_class.prototype.endDrag = function() {
    // Eliminates default action
}


/**
 * activity Class extends resource class
 */

function activity_class(id,group,config,parentObj) {
    this.init_activity(id,group,config,parentObj);
}
YAHOO.extend(activity_class, resource_class);

activity_class.prototype.init_activity = function(id,group,config,parentObj) {
    if (!id) { 
        YAHOO.log("Init activity, NO ID FOUND!",'error');
        return; 
    }    

    this.is = 'activity'; 
    this.currentGroup = this.get_current_group(id);

    this.init_resource(id,group,config,parentObj);

    this.groupButton= null;
    this.init_activity_button();

    if (this.debug)YAHOO.log("--init_activity "+id);         

}

activity_class.prototype.groupImages = ['/pix/t/groupn.gif','/pix/t/groups.gif','/pix/t/groupv.gif'];    

activity_class.prototype.init_activity_button = function() {         
    var button = main.mk_button('a',this.groupImages[this.currentGroup]); 
    YAHOO.util.Event.addListener(button,'click',this.toggle_group,this,true);
    this.commandContainer.appendChild(button);   
    this.groupButton = button;        
}    

activity_class.prototype.get_current_group = function(id) {
    if (document.getElementById(id) == null) {
        return;
    }

    var groupNodeArray = document.getElementById(id).getElementsByTagName('a');
    var groupNode = groupNodeArray[groupNodeArray.length-1];

    for (var x=0;x<this.groupImages.length;x++) {
        if (main.portal.wwwroot+this.groupImages[x] == groupNode.getElementsByTagName('img')[0].src) {
            return x;
        }
    }

    return 0;
}

activity_class.prototype.toggle_group = function() {
    this.currentGroup++;
    if (this.currentGroup > 2)
        this.currentGroup = 0;

    this.groupButton.getElementsByTagName('img')[0].src = main.portal.wwwroot + this.groupImages[this.currentGroup];

    main.connect('post','class=resource&field=groupmode',null,'value='+this.currentGroup+'&id='+this.id);
}    

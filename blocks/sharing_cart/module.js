/**
 *  Sharing Cart
 *  
 *  @author  VERSION2, Inc.
 *  @version $Id: module.js 908 2012-12-05 08:09:10Z malu $
 */
YUI.add('block_sharing_cart', function (Y)
{
    M.block_sharing_cart = new function ()
    {
        /** @var {Object}  The icon configurations */
        var icon = {
            // actions
            'backup'  : { css: 'editing_backup' , pix: 'i/backup'  },
            'movedir' : { css: 'editing_right'  , pix: 't/right'   },
            'move'    : { css: 'editing_move'   , pix: 't/move'    },
            'edit'    : { css: 'editing_update' , pix: 't/edit'    },
            'cancel'  : { css: 'editing_cancel' , pix: 't/delete'  },
            'delete'  : { css: 'editing_update' , pix: 't/delete'  },
            'restore' : { css: 'editing_restore', pix: 'i/restore' },
            // directories
            'dir-open'   : { pix: 'i/open'   },
            'dir-closed' : { pix: 'i/closed' }
        };
        /** @var {Object}  The shortcut to the strings */
        var str = Y.merge(M.str.moodle, M.str.block_sharing_cart);

        /** @var {Node}  The Sharing Cart block container node */
        var $block = Y.Node.one('.block_sharing_cart');

        /** @var {Object}  The current course */
        var course = {
            id: $block.one('a.editing_edit').get('href').match(/\/course\/view\.php\?id=(\d+)/)[1]
        };

        /**
         *  Shows an error message with given Ajax error
         *  
         *  @param {Object} response  The Ajax response
         */
        function show_error(response)
        {
            try {
                var ex = Y.JSON.parse(response.responseText);
                new M.core.exception({
                    name: str['pluginname'] + " - " + str['error'],
                    message: ex.message,
                    fileName: ex.file,
                    lineNumber: ex.line,
                    stack: ex.trace.replace(/\n/, '<br />')
                });
            } catch (e) {
                new M.core.exception({
                    name: str['pluginname'] + " - " + str['error'],
                    message: response.responseText
                });
            }
        }

        /**
         *  Get an action URL
         *  
         *  @param {String} name   The action name
         *  @param {Object} [args] The action parameters
         *  @return {String}
         */
        function get_action_url(name, args)
        {
            var url = M.cfg.wwwroot + '/blocks/sharing_cart/' + name + '.php';
            if (args) {
                var q = [];
                for (var k in args)
                    q.push(k + '=' + encodeURIComponent(args[k]));
                url += '?' + q.join('&');
            }
            return url;
        }

        /**
         *  Create a command icon
         *  
         *  @param {String} name  The command name, predefined in icon
         *  @param {String} [pix] The icon pix name to override
         */
        function create_command(name, pix)
        {
            return Y.Node.create('<a href="javascript:void(0)"/>')
                .addClass(icon[name].css)
                .set('title', str[name])
                .append(
                    Y.Node.create('<img class="iconsmall"/>')
                        .set('alt', str[name])
                        .set('src', M.util.image_url(pix || icon[name].pix))
                    );
        }

        /**
         *  Reload the Sharing Cart item tree
         */
        function reload_tree()
        {
            var $spinner = M.util.add_spinner(Y, $block.one('.commands'));
            Y.io(get_action_url('rest'), {
                method: 'POST',
                data: { 'action': 'render_tree' },
                on: {
                    start: function (tid) { $spinner.show(); },
                    end: function (tid) { $spinner.remove(); },
                    success: function (tid, response)
                    {
                        $block.one('.tree').replace(response.responseText);
                        M.block_sharing_cart.init_tree();
                    },
                    failure: function (tid, response) { show_error(response); }
                }
            });
        }

        /**
         *  Backup an activity
         *  
         *  @param {Integer} cmid
         *  @param {Boolean} userdata
         */
        function backup(cmid, userdata)
        {
            var $activity = Y.Node.one('#module-' + cmid);
            var $spinner = M.util.add_spinner(Y, $activity.one('.commands'));
            Y.io(get_action_url('rest'), {
                method: 'POST',
                data: { 'action': 'backup', 'cmid': cmid, 'userdata': userdata, 'sesskey': M.cfg.sesskey },
                on: {
                    start: function (tid) { $spinner.show(); },
                    end: function (tid) { $spinner.remove(); },
                    success: function (tid, response) { reload_tree(); },
                    failure: function (tid, response) { show_error(response); }
                }
            });
        }

        /**
         *  @class Targets for moving an item directory
         */
        var move_targets = new function ()
        {
            var $cancel = null, targets = [];
            
            /**
             *  Hide move targets
             */
            this.hide = function ()
            {
                if ($cancel) {
                    var $commands = $cancel.ancestor('.commands');
                    $cancel.remove();
                    $cancel = null;
                    $commands.ancestor('li.activity').setStyle('opacity', 1.0);
                    $commands.all('a').each(function () { this.show(); });
                    Y.Array.each(targets, function ($target) { $target.remove(); });
                    targets = [];
                }
            }
            /**
             *  Show move targets for a given item
             *  
             *  @param {Integer} id  The item ID
             */
            this.show = function (id)
            {
                this.hide();
                
                function move(e)
                {
                    var m = e.target.ancestor('a').get('className').match(/move-(\d+)-to-(\d+)/);
                    var id = m[1], to = m[2];
                    
                    Y.io(get_action_url('rest'), {
                        method: 'POST',
                        data: { 'action': 'move', 'id': id, 'to': to, 'sesskey': M.cfg.sesskey },
                        on: {
                            success: function (tid, response) { reload_tree(); },
                            failure: function (tid, response) { show_error(response); }
                        }
                    });
                }
                
                var $current = $block.one('#block_sharing_cart-item-' + id);
                var $indent = $current.one('div');
                var $next = $current.next();
                var $list = $current.ancestor('ul');
                
                var next_id = $next ? $next.get('id').match(/item-(\d+)$/)[1] : 0;
                
                function create_target(id, to)
                {
                    var $anchor = Y.Node.create('<a href="javascript:void(0)"/>')
                        .addClass('move-' + id + '-to-' + to)
                        .set('title', str['movehere'])
                        .append(
                            Y.Node.create('<img/>')
                                .set('alt', str['movehere'])
                                .set('src', M.util.image_url('movehere'))
                            );
                    var $target = Y.Node.create('<li class="activity"/>')
                        .append($indent.cloneNode(false).append($anchor));
                    $anchor.on('click', move, this);
                    return $target;
                }
                $list.all('> li.activity').each(function ($item)
                {
                    var to = $item.get('id').match(/item-(\d+)$/)[1];
                    if (to == id) {
                        $cancel = create_command('cancel', 't/left');
                        $cancel.on('click', this.hide, this);
                        var $commands = $item.one('.commands');
                        $commands.all('a').each(function () { this.hide(); });
                        $commands.append($cancel);
                        $item.setStyle('opacity', 0.5);
                    } else if (to != next_id) {
                        var $target = create_target(id, to);
                        $list.insertBefore($target, $item);
                        targets.push($target);
                    }
                }, this);
                if ($next) {
                    var $target = create_target(id, 0);
                    $list.append($target);
                    targets.push($target);
                }
            }
        }

        /**
         *  @class Targets for restoring an item
         */
        var restore_targets = new function ()
        {
            var $clipboard = null, targets = [];
            
            /**
             *  Hide restore targets
             */
            this.hide = function ()
            {
                if ($clipboard) {
                    $clipboard.remove();
                    $clipboard = null;
                    Y.Array.each(targets, function ($target) { $target.remove(); });
                    targets = [];
                }
            }
            /**
             *  Show restore targets for a given item
             *  
             *  @param {Integer} id  The item ID
             */
            this.show = function (id)
            {
                this.hide();
                
                var $item = $block.one('#block_sharing_cart-item-' + id);
                
                $clipboard = Y.Node.create('<div/>');
                var $cancel = create_command('cancel');
                var $view = $item.one('div').cloneNode(true).setStyle('display', 'inline');
                $view.one('.commands').remove();
                $cancel.on('click', this.hide, this);
                $clipboard.append(str['clipboard'] + ":").append($view).append($cancel);
                
                var $container = Y.Node.one('.course-content');
                $container.insertBefore($clipboard, $container.one('*'));
                
                $container.all(M.course.format.get_section_wrapper(Y)).each(function ($section)
                {
                    var section = $section.get('id').match(/(\d+)$/)[1];
                    var href = get_action_url('restore', {
                        'id'     : id,
                        'course' : course.id,
                        'section': section,
                        'sesskey': M.cfg.sesskey
                    });
                    var $target = Y.Node.create('<a/>')
                        .set('href', href)
                        .set('title', str['copyhere'])
                        .append(
                            Y.Node.create('<img/>')
                                .set('alt', str['copyhere'])
                                .set('src', M.util.image_url('movehere'))
                            );
                    $section.one('ul.section').append($target);
                    
                    targets.push($target);
                }, this);
            }
        }

        /**
         *  @class Directory states manager
         */
        var directories = new function ()
        {
            var KEY = 'block_sharing_cart-dirs';
            
            var opens = (Y.Cookie.get(KEY) + '').split(',').map(function (v) { return parseInt(v); });
            
            function save()
            {
                var expires = new Date();
                expires.setDate(expires.getDate() + 30);
                Y.Cookie.set(KEY, opens.join(','), { expires: expires });
            }
            function open($dir, visible)
            {
                var pix = icon[visible ? 'dir-open' : 'dir-closed'].pix;
                $dir.one('> div img').set('src', M.util.image_url(pix));
                $dir.one('> ul.list')[visible ? 'show' : 'hide']();
            }
            function toggle(e)
            {
                var $dir = e.target.ancestor('li.directory');
                var i = $dir.get('id').match(/(\d+)$/)[1];
                var v = $dir.one('> ul.list').getStyle('display') == 'none';
                
                open($dir, v);
                opens[i] = v ? 1 : 0;
                save();
            }
            
            /**
             *  Initialize directory states
             */
            this.init = function ()
            {
                var i = 0;
                $block.all('li.directory').each(function ($dir)
                {
                    $dir.set('id', 'block_sharing_cart-dir-' + i);
                    if (i >= opens.length)
                        opens.push(0);
                    else if (opens[i])
                        open($dir, true);
                    $dir.one('> div').setStyle('cursor', 'pointer').on('click', toggle, this);
                    i++;
                });
            }
            /**
             *  Reset directory states
             */
            this.reset = function ()
            {
                opens = [];
                this.init();
                save();
            }
        }

        /**
         *  Initialize the Sharing Cart block
         */
        this.init = function ()
        {
            str['pluginname'] = $block.one('h2').get('text');
            
            // arrange header icons (bulkdelete, help)
            $block.one('.header-commands').get('children').each(function ()
            {
                $block.one('.header .commands').append(this);
            });
            
            this.init_tree();
        }

        /**
         *  On backup command clicked
         *  
         *  @param {DOMEventFacade} e
         */
        this.on_backup = function (e)
        {
            var $activity = e.target.ancestor('li.activity');
            //var modtype = $activity.get('className').match(/modtype_(\w+)/)[1];
            var cmid = $activity.get('id').match(/(\d+)$/)[1];
            
            Y.io(get_action_url('rest'), {
                method: 'POST',
                data: { 'action': 'is_userdata_copyable', 'cmid': cmid },
                on: {
                    success: function (tid, response)
                    {
                        function embed_cmid(cmid)
                        {
                            return '<!-- #cmid=' + cmid + ' -->';
                        }
                        function parse_cmid(question)
                        {
                            return /#cmid=(\d+)/.exec(question)[1];
                        }
                        var copyable = response.responseText == '1';
                        if (copyable) {
                            var $yesnocancel = new M.block_sharing_cart.yesnocancel({
                                title: str['backup'],
                                question: str['confirm_userdata'] + embed_cmid(cmid),
                                yesLabel: str['yes'], noLabel: str['no'], cancelLabel: str['cancel']
                            });
                            $yesnocancel.on('complete-yes', function (e)
                            {
                                backup(parse_cmid(this.get('question')), true);
                            });
                            $yesnocancel.on('complete-no', function (e)
                            {
                                backup(parse_cmid(this.get('question')), false);
                            });
                        } else {
                            //if (confirm(str['confirm_backup']))
                            //    backup(cmid, false);
                            var $okcancel = new M.core.confirm({
                                title: str['backup'],
                                question: str['confirm_backup'] + embed_cmid(cmid),
                                yesLabel: str['ok'], noLabel: str['cancel']
                            });
                            $okcancel.on('complete-yes', function (e)
                            {
                                backup(parse_cmid(this.get('question')), false);
                            });
                        }
                    },
                    failure: function (tid, response) { show_error(response); }
                }
            });
        }

        /**
         *  On movedir command clicked
         *  
         *  @param {DOMEventFacade} e
         */
        this.on_movedir = function (e)
        {
            var $commands = e.target.ancestor('.commands');
            
            var $current_dir = $commands.ancestor('li.directory');
            var current_path = $current_dir ? $current_dir.one('div').get('title') : '/';
            
            var id = e.target.ancestor('li.activity').get('id').match(/(\d+)$/)[1];
            
            var dirs = [];
            $block.all('li.directory').each(function ()
            {
                dirs.push(this.one('div').get('title'));
            });
            
            var $form = Y.Node.create('<form/>').setStyle('display', 'inline');
            $form.set('action', 'javascript:void(0)');
            function submit(e)
            {
                var to = $form.one('[name="to"]').get('value');
                Y.io(get_action_url('rest'), {
                    method: 'POST',
                    data: { 'action': 'movedir', 'id': id, 'to': to, 'sesskey': M.cfg.sesskey },
                    on: {
                        success: function (tid, response) { reload_tree(); directories.reset(); },
                        failure: function (tid, response) { show_error(response); }
                    }
                });
            }
            $form.on('submit', submit);
            if (dirs.length == 0) {
                $form.append(
                    Y.Node.create('<input type="text" name="to"/>').set('value', current_path)
                    );
            } else {
                dirs.unshift('/');
                var $select = Y.Node.create('<select name="to"/>');
                for (var i = 0; i < dirs.length; i++) {
                    $select.append(Y.Node.create('<option/>').set('value', dirs[i]).append(dirs[i]));
                    if (dirs[i] == current_path)
                        $select.set('selectedIndex', i);
                }
                $select.on('change', submit);
                $form.append($select);
                
                var $edit = create_command('edit');
                $edit.on('click', function (e)
                {
                    var $input = Y.Node.create('<input type="text" name="to"/>').set('value', current_path);
                    $select.remove();
                    $edit.replace($input);
                    $input.focus();
                });
                $form.append($edit);
            }
            var $cancel = create_command('cancel');
            $cancel.on('click', function (e)
            {
                $form.remove();
                $commands.all('a').show();
            });
            $form.append($cancel);
            
            //$commands.all('a').hide();
            $commands.all('a').each(function () { this.hide(); });
            $commands.append($form);
        }

        /**
         *  On move command clicked
         *  
         *  @param {DOMEventFacade} e
         */
        this.on_move = function (e)
        {
            var $item = e.target.ancestor('li.activity');
            var id = $item.get('id').match(/(\d+)$/)[1];
            
            move_targets.show(id);
        }

        /**
         *  On delete command clicked
         *  
         *  @param {DOMEventFacade} e
         */
        this.on_delete = function (e)
        {
            if (!confirm(str['confirm_delete']))
                return;
            
            var $item = e.target.ancestor('li.activity');
            var id = $item.get('id').match(/(\d+)$/)[1];
            
            var $spinner = M.util.add_spinner(Y, e.target.ancestor('.commands'));
            
            Y.io(get_action_url('rest'), {
                method: 'POST',
                data: { 'action': 'delete', 'id': id, 'sesskey': M.cfg.sesskey },
                on: {
                    start: function (tid) { $spinner.show(); },
                    end: function (tid) { $spinner.remove(); },
                    success: function (tid, response) { $item.remove(); },
                    failure: function (tid, response) { show_error(response); }
                }
            });
        }

        /**
         *  On restore command clicked
         *  
         *  @param {DOMEventFacade} e
         */
        this.on_restore = function (e)
        {
            var $item = e.target.ancestor('li.activity');
            var id = $item.get('id').match(/(\d+)$/)[1];
            
            restore_targets.show(id);
        }

//        this.drag_start = function (e)
//        {
//            var drag = e.target;
//            drag.get('node').setStyles({
//                opacity: '.25'
//            });
//        }
//        this.drag_end = function (e)
//        {
//            var drag = e.target;
//            drag.get('node').setStyles({
//                opacity: '1'
//            });
//        }

        /**
         *  Initialize the Sharing Cart item tree
         */
        this.init_tree = function ()
        {
            // initialize items
            $block.all('li.activity').each(function ($item)
            {
//                // @see /course/yui/dragdrop/dragdrop.js
//                var drag = new Y.DD.Drag({
//                    node: $item,
//                    groups: [ 'block_sharing_cart' ],
//                    target: true,
//                    handles: [ 'img:first' ]
//                }).plug(Y.Plugin.DDProxy, {
//                    moveOnEnd: false
//                }).plug(Y.Plugin.DDConstrained, {
//                    constrain: '#page-content'
//                }).plug(Y.Plugin.DDWinScroll);
//                
//                drag.on('drag:start', this.drag_start, this);
//                drag.on('drag:end', this.drag_end, this);
                
                var $commands = $item.one('.commands');
                Y.Array.each([ 'movedir', 'move', 'delete', 'restore' ], function (action)
                {
                    var $command = create_command(action);
                    $command.on('click', this['on_' + action], this);
                    $commands.append($command);
                }, this);
            }, this);
            
            // initialize directories
            directories.init();
            
//            var drop = new Y.DD.Drop({
//                node: $block.one('ul.list'),
//                groups: [ 'resource' ]
//            });
        }

        /**
         *  Initialize resource drag drop
         */
        this.init_resource_dragdrop = function ()
        {
            var $sections = Y.Node.all('.course-content ' + M.course.format.get_section_wrapper(Y));
            $sections.each(function ($section)
            {
                $section.all('li.activity').each(function ($activity)
                {
                    var $backup = create_command('backup');
                    $activity.one('.commands').append($backup);
                    
                    $backup.on('click', this.on_backup, this);
                    
//                    var drag = Y.DD.DDM.getDrag($activity);
//                    var drop_hit = drag.getEvent('drag:drophit');
//                    
//                    var drag = new Y.DD.Drag({
//                        node: $activity.one('*'),
//                        groups: [ 'block_sharing_cart' ],
//                        target: true,
//                        handles: [ '.commands ' + css.backup ]
//                    }).plug(Y.Plugin.DDProxy, {
//                        moveOnEnd: false
//                    }).plug(Y.Plugin.DDConstrained, {
//                        constrain: '#page-content'
//                    }).plug(Y.Plugin.DDWinScroll);
//                    
//                    drag.on('drag:start', this.drag_start, this);
//                    drag.on('drag:end', this.drag_end, this);
//                    
//                    $icon.setStyle('cursor', 'move');
                }, this);
            }, this);
        }
    }

    /**
     *  Yes/No/Cancel confirmation dialogue
     *  
     *  @see /enrol/yui/notification/notification.js
     */
    var YESNOCANCEL = function (config)
    {
        YESNOCANCEL.superclass.constructor.apply(this, [config]);
    }
    Y.extend(YESNOCANCEL, M.core.confirm, {
        initializer: function (config)
        {
            var C = Y.Node.create;
            this.publish('complete');
            this.publish('complete-yes');
            this.publish('complete-no');
            this.publish('complete-cancel');
            var $yes    = C('<input type="button"/>').set('value', this.get('yesLabel'));
            var $no     = C('<input type="button"/>').set('value', this.get('noLabel'));
            var $cancel = C('<input type="button"/>').set('value', this.get('cancelLabel'));
            var $content = C('<div class="confirmation-dialogue"/>')
                .append(C('<div class="confirmation-message">' + this.get('question') + '</div>'))
                .append(C('<div class="confirmation-buttons"/>').append($yes).append($no).append($cancel));
            this.get('notificationBase').addClass('moodle-dialogue-confirm');
            this.setStdModContent(Y.WidgetStdMod.BODY, $content, Y.WidgetStdMod.REPLACE);
            this.setStdModContent(Y.WidgetStdMod.HEADER, this.get('title'), Y.WidgetStdMod.REPLACE);
            this.after('destroyedChange', function() { this.get('notificationBase').remove(); }, this);
            this._enterKeypress = Y.on('key', this.submit, window, 'down:13', this, true);
            this._escKeypress = Y.on('key', this.submit, window, 'down:27', this, false);
            $yes.on('click', this.submit, this, 'yes');
            $no.on('click', this.submit, this, 'no');
            $cancel.on('click', this.submit, this, 'cancel');
        },
        submit: function(e, outcome)
        {
            if (typeof outcome == "boolean") {
                // default is "no"
                outcome = outcome ? "no" : "cancel";
            }
            this._enterKeypress.detach();
            this._escKeypress.detach();
            this.fire('complete', outcome);
            this.fire('complete-' + outcome);
            this.hide();
            this.destroy();
        }
    }, {
        NAME: "Moodle yes-no-cancel dialogue",
        CSS_PREFIX: 'moodle-dialogue',
        ATTRS: {
            title      : { validator: Y.Lang.isString, value: "Confirm" },
            question   : { validator: Y.Lang.isString, value: "Are you sure?" },
            yesLabel   : { validator: Y.Lang.isString, value: "Yes" },
            noLabel    : { validator: Y.Lang.isString, value: "No" },
            cancelLabel: { validator: Y.Lang.isString, value: "Cancel" }
        }
    });
    Y.augment(YESNOCANCEL, Y.EventTarget);

    M.block_sharing_cart.yesnocancel = YESNOCANCEL;


    // install a hook to M.course.init_resource_dragdrop()
    // so that block_sharing_cart can get DD-ready activity elements
    var M_course_init_resource_dragdrop = M.course.init_resource_dragdrop;
    M.course.init_resource_dragdrop = function ()
    {
        M_course_init_resource_dragdrop.apply(M.course, arguments);
        
        M.block_sharing_cart.init_resource_dragdrop.call(M.block_sharing_cart);
    }
},
'2.4, release 1',
{
    requires: [ 'base', 'node', 'io', 'dom', 'cookie', 'dd', 'moodle-course-dragdrop' ]
});

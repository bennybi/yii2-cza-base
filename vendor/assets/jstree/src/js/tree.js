yii.tree = (function ($) {
    var pub = {
        modelClass: undefined,
        modelPk: undefined,
        controllerUrl: undefined,
        updateUrl: undefined,
        initProperties: function (properties) {
            $.each(properties, function (name, value) {
                pub[name] = value;
            });
        },
        overrideDefaults: function (userOptions) {
            var items = $.jstree.defaults.contextmenu.items();
            $.extend(true, items, userOptions);
            delete items.ccp;
            if (pub.updateUrl && items.update) {
                var label = items.update.label;
                items.update = {
                    separator_before: false,
                    separator_after: true,
                    _disabled: false,
                    label: label,
                    action: function (data) {
                        var inst = $.jstree.reference(data.reference);
                        var obj = inst.get_node(data.reference);

                        window.location.href = pub.updateUrl + '?' + pub.modelPk + '=' + obj.id;
                    }
                };
            }
            $.jstree.defaults.contextmenu.items = items;
        },
        openNode: function (event, target) {
            $.post(
                    pub.controllerUrl + '/open',
                    {
                        modelClass: pub.modelClass,
                        modelPk: target.node.id
                    }
            );
        },
        closeNode: function (event, target) {
            $.post(
                    pub.controllerUrl + '/close',
                    {
                        modelClass: pub.modelClass,
                        modelPk: target.node.id
                    }
            );
        },
        createNode: function (event, target) {
            $.post(
                    pub.controllerUrl + '/append-to',
                    {
                        modelClass: pub.modelClass,
                        parentPk: target.parent
                    },
                    function (data) {
                        $(event.target).jstree('set_id', target.node, data.pk);
                    }
            );
        },
        renameNode: function (event, target) {
            if (target.text != target.old) {
                $.post(
                        pub.controllerUrl + '/rename',
                        {
                            modelClass: pub.modelClass,
                            modelPk: target.node.id,
                            name: target.text
                        }
                );
            }
        },
        moveNode: function (event, target) {
            var $tree = $(event.target);
            var $prevNode = $tree.jstree('get_prev_dom', target.node, true);
            var $nextNode = $tree.jstree('get_next_dom', target.node, true);

            if ($prevNode) {
                $.post(
                        pub.controllerUrl + '/insert-after',
                        {
                            modelClass: pub.modelClass,
                            modelPk: target.node.id,
                            prevModelPk: $prevNode.attr('id')
                        }
                );
            } else if ($nextNode) {
                $.post(
                        pub.controllerUrl + '/insert-before',
                        {
                            modelClass: pub.modelClass,
                            modelPk: target.node.id,
                            nextModelPk: $nextNode.attr('id')
                        }
                );
            }

            if (target.old_parent != target.parent) {
                var $newParent = $tree.jstree('get_node', target.parent);
                if (!$newParent.state.opened) {
                    $.post(
                            pub.controllerUrl + '/prepend-to',
                            {
                                modelClass: pub.modelClass,
                                parentPk: target.parent,
                                modelPk: target.node.id
                            }
                    );
                }
            }
        },
        deleteNode: function (event, target) {
            $.post(
                    pub.controllerUrl + '/delete',
                    {
                        modelClass: pub.modelClass,
                        modelPk: target.node.id
                    }
            );
        }
    };

    return pub;
})(jQuery);

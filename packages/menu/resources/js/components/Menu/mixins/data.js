export default {
    error     : false,
    content   : [],
    translates: {
        newElement   : 'Link',
        errorHeader  : 'Error',
        deleteConfirm: 'Are you sure delete element ',
        addButton    : 'Add element',
        blocks       : 'Blocks',
        block        : 'Block',
        addBlock     : 'Add Block',
        row          : 'Row',
        addRowElement: 'Add Element'
    },
    modelParams: {
        id: {
            name: 'id',
            type: 'int'
        },
        parent_id: {
            name  : 'pid',
            type  : 'int',
            ignore: true
        },
        page_id: {
            name    : 'page_id',
            type    : 'int',
            isSelect: true
        },
        page_component_id: {
            name    : 'page_component_id',
            type    : 'int',
            isSelect: true,
            ref     : 'page_id'
        },
        icon_image: {
            name     : 'icon_image',
            type     : 'int',
            isPicture: true,
            ref      : 'icon_path'
        },
        icon_path: {
            name         : 'icon_path',
            type         : 'string',
            ignoreOnModal: true
        },
        link: {
            name: 'link',
            type: 'string'
        },
        url: {
            name  : 'url',
            type  : 'string',
            ignore: true
        },
        title: {
            name: 'name',
            type: 'string'
        }
    },
    defaultModel: {
        addLeafNodeDisabled: true,
        editNodeDisabled   : true,
        isLeaf             : false,
        link               : '',
        url                : '',
        active             : true,
        marked             : false,
        icon_image         : 0,
        page_id            : 0,
        page_component_id  : 0,
        params             : []
    }
};

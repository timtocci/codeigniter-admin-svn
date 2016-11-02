<html>
<head>
    <title>Editor Grids</title>
    <script type="text/javascript" src="<?=base_url()?>application/views/extjs/ext-base.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/views/extjs/ext-all.js"></script>
  	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/menu/EditableItem.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/menu/RangeMenu.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/menu/ListMenu.js"></script>


	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/GridFilters.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/filter/Filter.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/filter/StringFilter.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/filter/DateFilter.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/filter/ListFilter.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/filter/NumericFilter.js"></script>
	<script type="text/javascript" src="<?=base_url()?>application/views/extjs/ux/grid/filter/BooleanFilter.js"></script>
	<script>
    Ext.onReady(function(){
        Ext.BLANK_IMAGE_URL = '<?=base_url()?>application/views/extjs/images/s.gif';
        Ext.QuickTips.init();

		Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

      var links = Ext.data.Record.create([
			{name: 'id', type: 'int'},
	        'title',
            'name',
            {name: 'heading', type: 'float'}
        ]);

        var store_links = new Ext.data.Store({
            url: '<?=base_url()?>index.php/edit/data_get_links',
            reader: new Ext.data.JsonReader({
                root:'rows',
                totalProperty: 'results',
                id:'id'
            }, links)

        });


        var panorams = Ext.data.Record.create([
			{name: 'id', type: 'int'},
	        'file',
            'name',
            'adress',
            {name: 'heading', type: 'float'}
        ]);

        var store = new Ext.data.Store({
            url: '<?=base_url()?>index.php/edit/data_get',
            reader: new Ext.data.JsonReader({
                root:'rows',
                totalProperty: 'results',
                id:'id'
            }, panorams)
            
        });

		var filters = new Ext.ux.grid.GridFilters({
			local: false,
			filters:[
				{type: 'numeric',  dataIndex: 'id', phpMode: true},
				{type: 'string',  dataIndex: 'file', phpMode: true},
				{type: 'string',  dataIndex: 'name', phpMode: true},
				{type: 'string',  dataIndex: 'adress', phpMode: true}
				
		]});


        var title_edit = new Ext.form.TextField({
            allowBlank: false,
            maxLength: 200
        });

        var runtime_edit = new Ext.form.NumberField({
            allowNegative: false,
            allowDecimals: true
        });



        var grid = new Ext.grid.EditorGridPanel({
            renderTo: document.body,
            frame: true,
            title: 'Panorams Database',
            height: 550,
            
            enableColumnMove: false,
            store: store,
			viewConfig: { forceFit: true },
            clicksToEdit: 2,
            columns: [
			{header: "Id", dataIndex: 'id', width: 50},
			{header: "File name", dataIndex: 'file',width: 150},
			{header: "Name", dataIndex: 'name', editor: title_edit, width: 300},
	        {header: "Adress", dataIndex: 'adress', editor: title_edit, width: 300},
			{header: "Heading", dataIndex: 'heading', editor: runtime_edit},
            ],
			plugins: [filters],
            sm: new Ext.grid.RowSelectionModel({
                singleSelect: true,
				listeners: {
					rowselect : function(sm1, rowIndex, record )
					{
						store_links.load({
                        params: {
                            start: 0,
                            limit: 100,
							id: record.id
                        }
                    });
					}
				}
            }),
            listeners: {
                afteredit: function(e){
                    Ext.Ajax.request({
                        url: '<?=base_url()?>index.php/edit/data_update',
                        params: {
                            id: e.record.id,
                            field: e.field,
                            value: e.value
                        },
                        success: function(resp,opt) {
                            e.record.commit();
                        },
                        failure: function(resp,opt) {
                            e.record.reject();
                        }
                    });
                },
				cellclick : function(grid, rowIndex, columnIndex, e) {
				var record = grid.getStore().getAt(rowIndex);  // Get the Record
				//var fieldName = grid.getColumnModel().getDataIndex(columnIndex); // Get field name
				//var data = record.get(fieldName);
				//
				//alert(rowIndex);
				store_links.load({
                        params: {
                            start: 0,
                            limit: 100,
							id: record.id
                        }
                    });
				}
				,
				render: {
                fn: function(){
                    store.load({
                        params: {
                            start: 0,
                            limit: 50
                        }
                    });
                }
            }

            },
			    keys: [{
                key: 46,
                fn: function(key,e){
                    var sm = grid.getSelectionModel(),
                        sel = sm.getSelected();
                    if (sm.hasSelection()){
                        Ext.Msg.show({
                            title: 'Remove Panorama',
                            buttons: Ext.MessageBox.YESNOCANCEL,
                            msg: 'Remove ' + sel.data.name + '?',
                            fn: function(btn){
                                if (btn == 'yes'){
                                    Ext.Ajax.request({
                                        url: '<?=base_url()?>index.php/edit/pano_del',
                                        params: {
                                            id: sel.data.id
                                        },
                                        success: function(resp,opt) {
                                            grid.getStore().remove(sel);
											store_links.removeAll();
                                        },
                                        failure: function(resp,opt) {
                                            Ext.Msg.alert('Error','Unable to delete panorama');
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                ctrl: false,
                stopEvent: true
            }],
   
			bbar: new Ext.PagingToolbar({
            store: store,
            pageSize: 50,
            plugins: [filters]
			})

        });

        var title_edit2 = new Ext.form.TextField({
            allowBlank: false,
            maxLength: 200
        });

        var runtime_edit2 = new Ext.form.NumberField({
            allowNegative: false,
            allowDecimals: true
        });


        var grid2 = new Ext.grid.EditorGridPanel({
            renderTo: document.body,
            frame: true,
            title: 'Panorama Links',
            height: 300,
            enableColumnMove: false,
            store: store_links,
			viewConfig: { forceFit: true },
            clicksToEdit: 2,
            columns: [
			{header: "Id", dataIndex: 'id', width: 50},
			{header: "Title", dataIndex: 'title',width: 150, editor: title_edit2},
			{header: "Heading", dataIndex: 'heading', editor: runtime_edit2},
            ],
            sm: new Ext.grid.RowSelectionModel({
                singleSelect: true
            }),
            listeners: {
                afteredit: function(e){
                    Ext.Ajax.request({
                        url: '<?=base_url()?>index.php/edit/data_link_update',
                        params: {
                            id: e.record.id,
                            field: e.field,
                            value: e.value
                        },
                        success: function(resp,opt) {
                            e.record.commit();
                        },
                        failure: function(resp,opt) {
                            e.record.reject();
                        }
                    });
                }
            },
            keys: [{
                key: 46,
                fn: function(key,e){
                    var sm = grid2.getSelectionModel(),
						sel = sm.getSelected();
                    if (sm.hasSelection()){
                        Ext.Msg.show({
                            title: 'Remove Link',
                            buttons: Ext.MessageBox.YESNOCANCEL,
                            msg: 'Remove ' + sel.data.title + '?',
                            fn: function(btn){
                                if (btn == 'yes'){
                                    Ext.Ajax.request({
                                        url: '<?=base_url()?>index.php/edit/link_del',
                                        params: {
                                            id: sel.data.id
                                        },
                                        success: function(resp,opt) {
                                            grid2.getStore().remove(sel);
                                        },
                                        failure: function(resp,opt) {
                                            Ext.Msg.alert('Error','Unable to delete link');
                                        }
                                    });
                                }
                            }
                        });
                    }
                },
                ctrl: false,
                stopEvent: true
            }]

        });








    });
    </script>
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>application/views/extjs/resources/css/ext-all.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>application/views/extjs/images/style.css" />
	<link rel="stylesheet" type="text/css" href="<?=base_url()?>application/views/extjs/images/RangeMenu.css" />

</head>
<body>
</body>
</html>
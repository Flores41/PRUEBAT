<html>

<head>
	<meta
		http-equiv="Content-Type"
		content="text/html; charset=utf-8">
	<meta charset="UTF-8" />
	<title>EJEMPLO IUVADE</title>
	<link
		rel="stylesheet"
		type="text/css"
		href="extjs/resources/css/ext-all.css" />
	<link
		rel="stylesheet"
		type="text/css"
		href="extjs/example.css" />
	<link
		rel="stylesheet"
		type="text/css"
		href="extjs/ux/css/CheckHeader.css" />

	<script
		type="text/javascript"
		src="extjs/bootstrap.js"
		charset="utf-8"></script>
	<!-- -->
	<script
		type="text/javascript"
		src="resources/locale/ext-lang-es.js"
		charset="utf-8"></script>
	<script>
		Ext.onReady(function() {
			Ext.QuickTips.init();
			//! 
			Ext.define('App.model.Trabajador', {
				extend: 'Ext.data.Model',
				fields: [{
						name: 'tra_ide',
						type: 'int'
					},
					{
						name: 'tra_cod',
						type: 'int'
					},
					{
						name: 'tra_nom',
						type: 'string'
					},
					{
						name: 'tra_pat',
						type: 'string'
					},
					{
						name: 'tra_mat',
						type: 'string'
					},
					{
						name: 'est_ado',
						type: 'int'
					}
				],
				idProperty: 'tra_ide'
			});
			//! STORE DE TRABAJADORES
			var storeTrabajadores = Ext.create('Ext.data.Store', {
				model: 'App.model.Trabajador',
				storeId: 'Trabajadores',
				proxy: {
					type: 'ajax',
					api: {
						read: '../trabajadores/get_trabajadores.php',
						create: '../trabajadores/create_trabajador.php',
						update: '../trabajadores/actualizar_trabajador.php',
						destroy: '../trabajadores/eliminar_trabajador.php'
					},
					reader: {
						type: 'json',
						root: 'data',
						successProperty: 'success'
					},
					writer: {
						type: 'json',
						writeAllFields: true,
						allowSingle: false,
					}
				},
				autoLoad: true,
				autoSync: false
			});


			//! FORMULARIO Y VENTANA PARA NUEVO O MODIFICAR TRABAJADOR
			var formTrabajador = Ext.create('Ext.form.Panel', {
				bodyPadding: 10,
				border: false,
				defaults: {
					xtype: 'textfield',
					anchor: '100%',
					allowBlank: false
				},
				items: [{
						xtype: 'hiddenfield',
						name: 'tra_ide'
					},
					{
						fieldLabel: 'Código',
						name: 'tra_cod',
						xtype: 'numberfield',
						allowDecimals: false,
						minValue: 0
					},
					{
						fieldLabel: 'Nombre',
						name: 'tra_nom'
					},
					{
						fieldLabel: 'A. Paterno',
						name: 'tra_pat'
					},
					{
						fieldLabel: 'A. Materno',
						name: 'tra_mat'
					}
				]
			});
			//! VENTANA PARA EL FORMULARIO DE TRABAJADOR
			var winTrabajador = Ext.create('Ext.window.Window', {
				title: 'Trabajador',
				width: 350,
				modal: true,
				closeAction: 'hide',
				layout: 'fit',
				items: [formTrabajador],
				buttons: [{
						text: 'Guardar',
						handler: function() {
							var form = formTrabajador.getForm();
							if (!form.isValid()) return;

							var values = form.getValues();
							var id = values.tra_ide;

							if (id) {

								var rec = storeTrabajadores.getById(parseInt(id, 10));
								if (rec) {
									rec.set(values);
								}
							} else {

								storeTrabajadores.add(values);
							}

							storeTrabajadores.sync({
								success: function() {
									storeTrabajadores.load();
									form.reset();
									winTrabajador.hide();
								},
								failure: function() {
									Ext.Msg.alert('Error', 'No se pudo guardar el trabajador');
								}
							});
						}
					},
					{
						text: 'Cancelar',
						handler: function() {
							formTrabajador.getForm().reset();
							winTrabajador.hide();
						}
					}
				]
			});
			//! GRID DE TRABAJADORES 
			var gridTrabajadores = Ext.create('Ext.grid.Panel', {
				title: 'Trabajadores',
				region: 'center',
				store: storeTrabajadores,
				columns: [{
						header: 'Id',
						dataIndex: 'tra_ide',
						width: 50
					},
					{
						header: 'Código',
						dataIndex: 'tra_cod',
						flex: 1
					},
					{
						header: 'Nombre',
						dataIndex: 'tra_nom',
						flex: 1
					},
					{
						header: 'A. Paterno',
						dataIndex: 'tra_pat',
						flex: 1
					},
					{
						header: 'A. Materno',
						dataIndex: 'tra_mat',
						flex: 1
					},
					{
						header: 'Estado',
						dataIndex: 'est_ado',
						width: 70
					}
				],


				tbar: [{
						text: 'Nuevo',
						handler: function() {
							var form = formTrabajador.getForm();
							form.reset();
							winTrabajador.setTitle('Nuevo trabajador');
							winTrabajador.show();
						}
					},
					{
						text: 'Modificar',
						handler: function() {
							var sel = gridTrabajadores.getSelectionModel().getSelection()[0];
							if (!sel) {
								Ext.Msg.alert('Atención', 'Seleccione un trabajador');
								return;
							}
							var form = formTrabajador.getForm();
							form.loadRecord(sel);
							winTrabajador.setTitle('Modificar trabajador');
							winTrabajador.show();
						}
					},
					{
						text: 'Eliminar',
						handler: function() {
							var sel = gridTrabajadores.getSelectionModel().getSelection()[0];
							if (!sel) {
								Ext.Msg.alert('Atención', 'Seleccione un trabajador');
								return;
							}

							Ext.Msg.confirm('Confirmar', 'Eliminar trabajador?', function(btn) {
								if (btn !== 'yes') return;

								storeTrabajadores.remove(sel);
								storeTrabajadores.sync({
									success: function() {
										storeTrabajadores.load();
									},
									failure: function() {
										Ext.Msg.alert('Error', 'No se pudo eliminar el trabajador');
										storeTrabajadores.rejectChanges();
									}
								});
							});
						}
					}
				],


			});
			//! PANEL PRINCIPAL DE TRABAJADORES
			Ext.create('Ext.panel.Panel', {
				layout: 'border',
				width: 900,
				height: 400,
				renderTo: Ext.getBody(),
				items: [gridTrabajadores]
			});



			//! PROBLEMA 2
			//! MODULO DE VENTAS
			var storeVentas = Ext.create('Ext.data.Store', {
				storeId: 'Ventas',
				fields: ['ven_ide', 'ven_ser', 'ven_num', 'ven_cli', 'ven_mon', 'est_ado'],
				proxy: {
					type: 'ajax',
					url: '../ventas/get_ventas.php',
					reader: {
						type: 'json',
						root: 'data'
					}
				},
				autoLoad: true
			});
			//! STORE PARA EL DETALLE DE VENTA
			var storeDetalle = Ext.create('Ext.data.Store', {
				storeId: 'DetalleVenta',
				fields: ['v_d_ide', 'ven_ide', 'v_d_pro', 'v_d_uni', 'v_d_can', 'v_d_tot', 'est_ado'],
				proxy: {
					type: 'ajax',
					url: '../ventas/get_detalles_venta.php',
					reader: {
						type: 'json',
						root: 'data'
					}
				},
				autoLoad: false
			});
			//! GRID 1 DE INFORMACION DE VENTAS
			var gridCabecera = Ext.create('Ext.grid.Panel', {
				title: 'Información Ventas',
				store: storeVentas,
				region: 'north',
				height: 220,
				columns: [{
						header: 'ID',
						dataIndex: 'ven_ide',
						width: 60
					},
					{
						header: 'Serie',
						dataIndex: 'ven_ser',
						width: 80
					},
					{
						header: 'Número',
						dataIndex: 'ven_num',
						flex: 1
					},
					{
						header: 'Cliente',
						dataIndex: 'ven_cli',
						flex: 1
					},
					{
						header: 'Monto',
						dataIndex: 'ven_mon',
						width: 100
					},
					{
						header: 'Estado',
						dataIndex: 'est_ado',
						width: 70
					}
				]
			});
			//! GRID  2 DE DETALLES DE VENTAS
			var gridDetalle = Ext.create('Ext.grid.Panel', {
				title: 'Detalle de la venta',
				store: storeDetalle,
				region: 'center',
				columns: [{
						header: 'ID Det',
						dataIndex: 'v_d_ide',
						width: 70
					},
					{
						header: 'Producto',
						dataIndex: 'v_d_pro',
						flex: 1
					},
					{
						header: 'P. Unit.',
						dataIndex: 'v_d_uni',
						width: 80
					},
					{
						header: 'Cant.',
						dataIndex: 'v_d_can',
						width: 80
					},
					{
						header: 'Total',
						dataIndex: 'v_d_tot',
						width: 80
					},
					{
						header: 'Estado',
						dataIndex: 'est_ado',
						width: 70
					}
				]
			});
			//! AL SELECCIONAR UNA VENTA SE CARGA EL DETALLE
			gridCabecera.getSelectionModel().on('select', function(selModel, record) {
				var venId = record.get('ven_ide');
				storeDetalle.getProxy().extraParams = {
					ven_ide: venId
				};
				storeDetalle.load();
			});


			//! FORMULARIO DE NUEVA VENTA
			var formVenta = Ext.create('Ext.form.Panel', {
				bodyPadding: 10,
				border: false,
				defaults: {
					xtype: 'textfield',
					anchor: '100%',
					allowBlank: false,
					labelWidth: 80
				},
				items: [{
						xtype: 'hiddenfield',
						name: 'ven_ide'
					},
					{
						fieldLabel: 'Serie',
						name: 'ven_ser',
						maxLength: 5
					},
					{
						fieldLabel: 'Número',
						name: 'ven_num',
						maxLength: 100
					},
					{
						fieldLabel: 'Cliente',
						name: 'ven_cli'
					},
					{
						xtype: 'numberfield',
						fieldLabel: 'Monto',
						name: 'ven_mon',
						decimalPrecision: 2,
						allowBlank: true,
						readOnly: true,
						value: 0
					}
				]
			});

			//! FORMULARIO DE DETALLE DE VENTA
			var formDetalle = Ext.create('Ext.form.Panel', {
				bodyPadding: 10,
				border: false,
				layout: 'hbox',
				defaults: {
					margin: '0 5 0 0'
				},
				items: [{
						xtype: 'textfield',
						fieldLabel: 'Producto',
						name: 'v_d_pro',
						flex: 2,
						allowBlank: false,
						labelWidth: 60
					},
					{
						xtype: 'numberfield',
						fieldLabel: 'P. Unit',
						name: 'v_d_uni',
						flex: 1,
						decimalPrecision: 2,
						allowBlank: false,
						labelWidth: 50
					},
					{
						xtype: 'numberfield',
						fieldLabel: 'Cant',
						name: 'v_d_can',
						flex: 1,
						decimalPrecision: 2,
						allowBlank: false,
						labelWidth: 40
					},
					{
						xtype: 'button',
						text: 'Agregar',
						handler: function() {
							var cabeceraForm = formVenta.getForm();
							var detalleForm = formDetalle.getForm();

							if (!cabeceraForm.isValid() || !detalleForm.isValid()) {
								return;
							}

							var cabVals = cabeceraForm.getValues();

							if (!cabVals.ven_ide) {
								Ext.Msg.alert('Atención', 'Primero guarda la cabecera de la venta.');
								return;
							}

							var detVals = detalleForm.getValues();

							Ext.Ajax.request({
								url: '../ventas/guardar_detalle_venta.php',
								method: 'POST',
								jsonData: {
									ven_ide: parseInt(cabVals.ven_ide, 10),
									v_d_pro: detVals.v_d_pro,
									v_d_uni: detVals.v_d_uni,
									v_d_can: detVals.v_d_can
								},
								success: function(response) {
									detalleForm.reset();
									storeDetalle.getProxy().extraParams = {
										ven_ide: cabVals.ven_ide
									};
									storeDetalle.load();
									storeVentas.load();
								},
								failure: function() {
									Ext.Msg.alert('Error', 'No se pudo guardar el detalle');
								}
							});
						}
					}
				]
			});
			//! GRID DE DETALLE DE VENTANA PERO DENTRO DE LA VENTANA DE VENTA
			var gridDetalleWin = Ext.create('Ext.grid.Panel', {
				title: 'Detalle de la venta',
				store: storeDetalle,
				height: 30,
				columns: [{
						header: 'ID Det',
						dataIndex: 'v_d_ide',
						width: 70
					},
					{
						header: 'Producto',
						dataIndex: 'v_d_pro',
						flex: 1
					},
					{
						header: 'P. Unit.',
						dataIndex: 'v_d_uni',
						width: 80
					},
					{
						header: 'Cant.',
						dataIndex: 'v_d_can',
						width: 80
					},
					{
						header: 'Total',
						dataIndex: 'v_d_tot',
						width: 80
					}
				]
			});
			//! VENTANA PARA GUARDAR UNA VENTA O ACTUALIZARLA
			var winVenta = Ext.create('Ext.window.Window', {
				title: 'Venta',
				width: 700,
				height: 500,
				modal: true,
				closeAction: 'hide',
				layout: 'border',
				items: [{
						region: 'north',
						layout: 'fit',
						height: 160,
						items: [formVenta]
					},
					{
						region: 'center',
						layout: 'border',
						items: [{
								region: 'north',
								layout: 'fit',
								height: 80,
								items: [formDetalle]
							},
							{
								region: 'center',
								layout: 'fit',
								items: [gridDetalleWin]
							}
						]
					}
				],
				buttons: [{
						text: 'Guardar cabecera',
						handler: function() {
							var form = formVenta.getForm();
							if (!form.isValid()) return;

							var vals = form.getValues();

							Ext.Ajax.request({
								url: '../ventas/guardar_venta.php',
								method: 'POST',
								jsonData: {
									ven_ide: vals.ven_ide || null,
									ven_ser: vals.ven_ser,
									ven_num: vals.ven_num,
									ven_cli: vals.ven_cli,
									ven_mon: vals.ven_mon || 0
								},
								success: function(response) {
									var resp = Ext.decode(response.responseText);
									if (resp.success) {
										form.setValues({
											ven_ide: resp.ven_ide
										});
										storeVentas.load();
										Ext.Msg.alert('OK', 'Cabecera guardada');
									} else {
										Ext.Msg.alert('Error', resp.error || 'Error al guardar');
									}
								},
								failure: function() {
									Ext.Msg.alert('Error', 'No se pudo guardar la venta');
								}
							});
						}
					},
					{
						text: 'Cerrar',
						handler: function() {
							formVenta.getForm().reset();
							formDetalle.getForm().reset();
							storeDetalle.removeAll();
							winVenta.hide();
						}
					}
				]
			});

			//! PANEL PRINCIPAL DE VENTAS CON LOS BOTONES DE NUEVO, MODIFICAR Y ELIMINAR
			var panelVentas = Ext.create('Ext.panel.Panel', {
				title: 'Módulo de Ventas',
				layout: 'border',
				width: 900,
				height: 500,
				renderTo: Ext.getBody(),
				items: [{
					region: 'center',
					layout: 'border',
					items: [gridCabecera, gridDetalle]
				}],
				tbar: [{
						text: 'Nuevo',
						handler: function() {
							formVenta.getForm().reset();
							formDetalle.getForm().reset();
							storeDetalle.removeAll();
							winVenta.setTitle('Nueva venta');
							winVenta.show();
						}
					},
					{
						text: 'Modificar',
						handler: function() {
							var sel = gridCabecera.getSelectionModel().getSelection()[0];
							if (!sel) {
								Ext.Msg.alert('Atención', 'Seleccione una venta');
								return;
							}

							formVenta.getForm().setValues({
								ven_ide: sel.get('ven_ide'),
								ven_ser: sel.get('ven_ser'),
								ven_num: sel.get('ven_num'),
								ven_cli: sel.get('ven_cli'),
								ven_mon: sel.get('ven_mon')
							});

							storeDetalle.getProxy().extraParams = {
								ven_ide: sel.get('ven_ide')
							};
							storeDetalle.load();

							formDetalle.getForm().reset();
							winVenta.setTitle('Modificar venta');
							winVenta.show();
						}
					},
					{
						text: 'Eliminar',
						handler: function() {
							var sel = gridCabecera.getSelectionModel().getSelection()[0];
							if (!sel) {
								Ext.Msg.alert('Atención', 'Seleccione una venta');
								return;
							}

							Ext.Msg.confirm('Confirmar', '¿Eliminar la venta seleccionada?', function(btn) {
								if (btn !== 'yes') return;

								Ext.Ajax.request({
									url: '../ventas/eliminar_venta.php',
									method: 'GET',
									params: {
										ven_ide: sel.get('ven_ide')
									},
									success: function() {
										storeVentas.load();
										storeDetalle.removeAll();
									},
									failure: function() {
										Ext.Msg.alert('Error', 'No se pudo eliminar la venta');
									}
								});
							});
						}
					}
				]
			});

		});
	</script>
</head>
</body>

</html>
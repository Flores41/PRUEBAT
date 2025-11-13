

# Prueba Técnica - IUVADE SRL
## Solución a Formularios CRUD con ExtJS, PHP y PostgreSQL

![Captura del sistema](https://i.ibb.co/FLDtqSsq/screenshot.png)


1.  **Formulario 1:** Un CRUD simple para la tabla `trabajador`.
2.  **Formulario 2:** Un CRUD  para `venta` y `venta_detalle`, incluyendo la lógica de actualización de totales mediante triggers.

###  Estructura de Carpetas

* `/db.php`: Archivo de conexión central a la BD.
* `/ejemplo/`: Contiene el **Front-end** (`index.php`) y la librería `extjs`.
* `/trabajadores/`: Contiene todas las APIs de PHP para el Formulario 1.
* `/ventas/`: Contiene todas las APIs de PHP para el Formulario 2.

---

###  Script de Base de Datos


```sql
CREATE SCHEMA prueba;


CREATE TABLE prueba.trabajador(
  tra_ide serial PRIMARY KEY,
  tra_cod integer DEFAULT 0,
  tra_nom varchar(200) DEFAULT '',
  tra_pat varchar(200) DEFAULT '',
  tra_mat varchar(200) DEFAULT '',
  est_ado integer DEFAULT 1
);


CREATE TABLE prueba.venta(
  ven_ide serial PRIMARY KEY,
  ven_ser varchar(5) DEFAULT '',
  ven_num varchar(100) DEFAULT '',
  ven_cli text DEFAULT '',
  ven_mon numeric (14,2),
  /*  Añadí este campo que faltaba en el script original */
  est_ado integer DEFAULT 1 
);


CREATE TABLE prueba.venta_detalle(
  v_d_ide serial PRIMARY KEY,
  ven_ide integer,
  v_d_pro text DEFAULT '',
  v_d_uni numeric(14,2) DEFAULT 0.00,
  v_d_can numeric(14,2) DEFAULT 0.00,
  v_d_tot numeric(14,2) DEFAULT 0.00,
  est_ado integer DEFAULT 1
);

CREATE OR REPLACE FUNCTION prueba.fn_calcular_detalle_total()
RETURNS trigger AS $$
BEGIN
    NEW.v_d_tot := COALESCE(NEW.v_d_can, 0) * COALESCE(NEW.v_d_uni, 0);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trg_calcular_detalle_total
BEFORE INSERT OR UPDATE ON prueba.venta_detalle
FOR EACH ROW
EXECUTE PROCEDURE prueba.fn_calcular_detalle_total();
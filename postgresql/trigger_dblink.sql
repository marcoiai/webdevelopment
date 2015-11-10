
--DROP TRIGGER synchTable on tablename;

CREATE OR REPLACE FUNCTION synchTable() RETURNS TRIGGER
AS $synchTable$
    DECLARE
        conn			  text;
        queryTemp		text;
        res			    text;
        fromPk			text[];
        toPk			  text[];
    BEGIN

	conn   := (SELECT * FROM dblink_connect('host=host.com.br port=5432 dbname=dbname user=dbuser password=dbpassword') as t1(text));
	fromPk := ARRAY[NEW.id::text];
  toPk   := ARRAY[NEW.id::text];

        NEW.column := FALSE;

        IF (TG_OP = 'DELETE') THEN

            -- for now we don't need to intervent deletions as we update deleted_at to remove rows

        ELSIF (TG_OP = 'UPDATE') THEN

		queryTemp := (SELECT dblink_build_sql_update('tablename'::text,'1'::int2vector, 1, fromPk, toPk));

		res := (SELECT dblink_exec(queryTemp));

                NEW.column := TRUE;

        ELSIF (TG_OP = 'INSERT') THEN

	  queryTemp := (SELECT dblink_build_sql_insert('tablename'::text,'1'::int2vector, 1, fromPk, toPk));

          res := (SELECT dblink_exec(queryTemp));

          NEW.column := TRUE;

        END IF;

        RETURN NEW;

        EXCEPTION WHEN OTHERS THEN
		NEW.column := FALSE;
		RETURN NEW;
	--WHEN sqlclient_unable_to_establish_sqlconnection THEN

    RETURN NEW;
    END;
$synchTable$ LANGUAGE plpgsql;

CREATE TRIGGER synchTable
AFTER INSERT OR UPDATE OR DELETE ON tablename
    FOR EACH ROW EXECUTE PROCEDURE synchTable();
    

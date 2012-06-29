DROP FUNCTION IF EXISTS nota_distribuida(integer);
CREATE OR REPLACE FUNCTION nota_distribuida(integer) RETURNS double precision
    AS $_$select sum(nota_distribuida)
        from diario_formulas
        where
            grupo ilike '%-' || $1$_$
    LANGUAGE sql;

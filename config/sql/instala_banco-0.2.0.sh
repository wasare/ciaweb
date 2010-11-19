#!/bin/bash

echo
read -p "usuário administrador do PostgreSQL: " USUARIO_ADMIN
[ -z "$USUARIO_ADMIN" ] &&  echo "usuário inválido!" && exit 2

echo
read -p "senha do usuário administrador: " ACESSO
[ -z "$ACESSO" ] &&  echo "Senha não informada!" && exit 2

echo
read -p "endereço do host onde esta o servidor PostgreSQL: " DB_HOST
[ -z "$DB_HOST" ] &&  echo "host não informado!" && exit 2

echo
read -p "nome do banco de dados para o sistema: " BANCO
[ -z "$BANCO" ] &&  echo "nome para o banco não informado!" && exit 2

echo
read -p "nome do usuário para o acessar o banco ${BANCO} (usuário e banco serão criados): " USUARIO
[ -z "$USUARIO" ] &&  echo "Senha não informada!" && exit 2


export PGPASSWORD=$ACESSO

echo "informe uma senha para o usuário ${USUARIO}: "
createuser -P -S -D -R -U ${USUARIO_ADMIN} -h ${DB_HOST} ${USUARIO}

echo "informe uma senha para o usuário aluno (para acesso aos dados da área do aluno): "
createuser -P -S -D -R -U ${USUARIO_ADMIN} -h ${DB_HOST} aluno

createdb -U ${USUARIO_ADMIN} -h ${DB_HOST} -T template0 -E UTF8 --lc-collate=pt_BR.UTF-8 --lc-ctype=pt_BR.UTF-8 -O ${USUARIO} ${BANCO}
#createdb -U ${USUARIO_ADMIN} -h ${DB_HOST} -E LATIN1 -O ${USUARIO} ${BANCO}
createlang -U ${USUARIO_ADMIN} -h ${DB_HOST} plpgsql ${BANCO}

echo
read -p "informe novamente a senha o usuário ${USUARIO}: " ACESSO
[ -z "$ACESSO" ] &&  echo "Senha não informada!" && exit 2

export PGPASSWORD=$ACESSO


echo "" > sa_instalacao.log

ARQUIVOS=`(cd  banco-0.2.0 && ls *.sql | sort -n)`
for f in ${ARQUIVOS}
do
     psql -h ${DB_HOST} -U ${USUARIO} -d ${BANCO} <  banco-0.2.0/$f 2>> sa_instalacao.log
done


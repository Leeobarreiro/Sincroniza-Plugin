# Sincroniza-Plugin
Plugin para sincronizar a tabela Aluno_info do moodle para o wordpress.
Contendo: 

- cursos que o aluno está inscrito

- progresso em cada curso

- coortes que está inscrito

- conclusão de cursos

- campo adicional: carga horaria

- campo adicional: pontos

O plugin ira sincronizar as informações com a tabela sempre que o cron for executado.

Passo a passo:

1) Deverá ser criado no banco de dados uma tabela chamada: "Aluno_info"

2) Importar para a tabela:

* userid
* courseid
* coursename
* progress
* completion

3) Fazer a instalação do plugin 

4) Você pode alterar a data de atualização do Cron nas tarefas agendadas, caso queira. Vem 15 minutos por padrão

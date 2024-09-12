FROM mariadb:10.6-ubi

COPY init_db.sql /docker-entrypoint-initdb.d/

USER root
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["start.sh"]
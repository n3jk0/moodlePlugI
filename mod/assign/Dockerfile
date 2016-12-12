FROM java:8

ARG file1
ARG assign

COPY local/$assign /$assign/

RUN javac $file1 2> error.txt ;exit 0
RUN if [ -z $(cat error.txt  &> /dev/null ) ] ; then echo empty; else exit 1; fi

ENTRYPOINT ["java"]

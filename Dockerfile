FROM ubuntu:latest
LABEL authors="jakubowsianka"

ENTRYPOINT ["top", "-b"]

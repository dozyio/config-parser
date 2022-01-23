NAME := dozyio/config-parser
TAG := $$(git rev-parse --short HEAD)
IMG := ${NAME}:${TAG}
LATEST := ${NAME}:latest

build:
	docker build -t ${IMG} .
	docker tag ${IMG} ${LATEST}

test:
	docker run --rm ${IMG}

all: build test

.PHONY: .build .test

#!/bin/bash
echo Creating user $1...
useradd -p $2 -g $3 -e $4 -M $1
echo User created.

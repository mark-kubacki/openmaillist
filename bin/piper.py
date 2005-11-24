#!/usr/bin/env python
from email.Parser import Parser
import sys

msg = sys.stdin.read()

p = Parser()
emailMessage = p.parsestr(msg)
headers = p.parsestr(msg, True)

print headers['message-id']
print headers['from']
print headers['in-reply-to']
print headers['references']
print headers['subject']

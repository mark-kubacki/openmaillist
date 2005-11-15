#!/usr/bin/perl -w
use strict;
use Email::Simple;

my $message = '';
while(<>) {
    $message .= $_;
}


my $obj = Email::Simple->new($message);
print $obj->header("Message-ID")."\n";
print $obj->header("From")."\n";
print $obj->header("In-Reply-To")."\n";
print $obj->header("References")."\n";
print $obj->header("Subject")."\n";

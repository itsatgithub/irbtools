#!/usr/bin/env python3

import yaml # this manages yml files
import datetime
import smtplib # this manages emails

from pathlib import Path
from email.mime.text import MIMEText # import the email modules

today = datetime.date.today() # this is used later on the generation of the file name

with open('backup-monitor-files.yml', 'r') as ymlfile:
	cfg = yaml.load(ymlfile)

# sending email?
send_email = False
text_email = ''

for section in cfg:
	for filename in cfg[section]['backupfiles']:
		filestr = str(filename)
		filestr = filestr.replace('yyyy', str(today.year))
		filestr = filestr.replace('mm', str(today.month))
		filestr = filestr.replace('dd', str(today.day))

		backupfile = Path(filestr)

		if not backupfile.exists():
			text_email += 'File not found %s on %s \r' % (filestr, section)
			send_email = True

if send_email:
	with open('textmail.txt') as fp:
		# create a text/plain message
		#msg = MIMEText(fp.read())
		msg = MIMEText(text_email)

	msg['Subject'] = 'This is the subject'
	msg['From'] = 'its@irbbarcelona.org'
	msg['To'] = 'roberto.bartolome@irbbarcelona.org'

	# send the email via SMTP server
	s = smtplib.SMTP('smtp.pcb.ub.es')
	s.send_message(msg)
	s.quit()
#!/usr/bin/env python3

import yaml
import datetime
from pathlib import Path

today = datetime.date.today()

with open('backup-monitor-files.yml', 'r') as ymlfile:
	cfg = yaml.load(ymlfile)

for section in cfg:
	for filename in cfg[section]['backupfiles']:
		filestr = str(filename)
		filestr = filestr.replace('yyyy', str(today.year))
		filestr = filestr.replace('mm', str(today.month))
		filestr = filestr.replace('dd', str(today.day))

		backupfile = Path(filestr)

		if backupfile.is_file():
		  print('Backup ok on', section)
		else:
		  print('Backupfile error', filestr, 'on', section)


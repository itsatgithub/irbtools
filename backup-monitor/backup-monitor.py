#!/usr/bin/env python3

import yaml
import datetime
from pathlib import Path

today = datetime.date.today()

with open('backup-monitor-files.yml', 'r') as ymlfile:
	cfg = yaml.load(ymlfile)

for section in cfg:
	filename = cfg[section]['backupfile']
	filename = filename.replace('yyyy', str(today.year))
	filename = filename.replace('mm', str(today.month))
	filename = filename.replace('dd', str(today.day))

	backupfile = Path(filename)

	if backupfile.is_file():
	  print('Existe ', filename)
	else:
	  print('No Existe ', filename)


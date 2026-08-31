#!/usr/bin/env python3
"""Upload SMS2 deploy staging folder to InfinityFree via FTPS."""
import os
import sys
from ftplib import FTP_TLS, error_perm

HOSTS = ['ftpupload.net', 'ftp.epizy.com']
USER = 'if0_42794375'
PASS = sys.argv[1] if len(sys.argv) > 1 else os.environ.get('SMS2_FTP_PASS', '')
LOCAL = r'C:\xampp\htdocs\sms2_deploy_staging'
REMOTE_ROOT = '/htdocs'

if not PASS:
    print('Usage: python ftp-upload.py PASSWORD')
    sys.exit(1)

def upload_tree(ftp, local_dir, remote_dir):
    try:
        ftp.mkd(remote_dir)
    except error_perm:
        pass
    ftp.cwd(remote_dir)
    for name in os.listdir(local_dir):
        local_path = os.path.join(local_dir, name)
        if os.path.isdir(local_path):
            upload_tree(ftp, local_path, name)
            ftp.cwd('..')
        else:
            print(f'Uploading {remote_dir}/{name}')
            with open(local_path, 'rb') as f:
                ftp.storbinary(f'STOR {name}', f)

for host in HOSTS:
    print(f'Trying {host}...')
    try:
        ftp = FTP_TLS()
        ftp.connect(host, 21, timeout=30)
        ftp.auth()
        ftp.prot_p()
        ftp.login(USER, PASS)
        print(f'Logged in to {host}')
        upload_tree(ftp, LOCAL, REMOTE_ROOT)
        ftp.quit()
        print('Upload complete.')
        sys.exit(0)
    except Exception as e:
        print(f'Failed on {host}: {e}')

sys.exit(1)

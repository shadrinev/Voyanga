from fabric.api import run, env, get, local

env.hosts = ['voyanga.com']
env.user = 'voyanga'
env.shell = '/bin/sh -c'

def uptest():
    """
    Updates test.voyanga.com
    """
    run('cd test && git pull')

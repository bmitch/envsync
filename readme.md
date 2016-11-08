# Env Syncer #
[![Build Status](https://travis-ci.org/bmitch/envsync.svg?branch=master)](https://travis-ci.org/bmitch/envsync)
[![Code Climate](https://codeclimate.com/github/bmitch/envsync/badges/gpa.svg)](https://codeclimate.com/github/bmitch/envsync)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bmitch/envsync/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bmitch/envsync/?branch=master)
[![codecov](https://codecov.io/gh/bmitch/envsync/branch/master/graph/badge.svg)](https://codecov.io/gh/bmitch/envsync)

## What is it? ##
envsync is a tool that can be used in your CI and/or deploy build scripts to help make sure your environment variables that are defined in your Laravel source code, .env and .env.example files are up to date.

## How to install ##

`composer require --dev bmitch/envsync`

## How to use ###

envsync has 3 different modes:

### Default Mode ###
Run `vendor/bin/envsync <folder>` where `<folder>` is where your source code is. You will see the following report like this:
```
EnvSyncer Report - https://github.com/bmitch/envsync
+----------+-----------+-----------------+---------+
| Variable | In Source | In .env.example | In .env |
+----------+-----------+-----------------+---------+
| FOO      | No        | No              | Yes     |
| BAR      | No        | Yes             | No      |
| BAZ      | Yes       | No              | No      |
+----------+-----------+-----------------+---------+
```

### CI Mode ###
Run `vendor/bin/envsync <folder> ci ` where `<folder>` is where your source code is. You will see the same report as above but without the "In .env" column:
```
EnvSyncer Report - https://github.com/bmitch/envsync
+----------+-----------+-----------------+
| Variable | In Source | In .env.example |
+----------+-----------+-----------------+
| FOO      | No        | No              |
| BAR      | No        | Yes             |
| BAZ      | Yes       | No              |
+----------+-----------+-----------------+
```

If any of the environemnt variables defined in your source code are NOT defined in your `.env.example` file then the command will exit with a 1, failing your CI script.

### Deploy Mode ###
Run `vendor/bin/envsync <folder> deploy ` where `<folder>` is where your source code is. You will see the same report as the first one above but without the "In .env.example" column:

```
EnvSyncer Report - https://github.com/bmitch/envsync
+----------+-----------+---------+
| Variable | In Source | In .env |
+----------+-----------+---------+
| FOO      | No        | Yes     |
| BAR      | No        | No      |
| BAZ      | Yes       | No      |
+----------+-----------+---------+
```

If any of the environment variables defined in your source code are NOT defined in your `.env` file then the command will exit with a 1, failing your deploy script.


#### Bugs, Features, Fixes, Feedback, Comments ###
Please feel free to contribute.
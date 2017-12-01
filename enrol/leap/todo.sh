#!/bin/bash

clear

echo -e "\n\e[1;36mTODO Finder: \e[0;36mSummary\e[0m"
#grep -iIlr --color --exclude="todo.sh" 'todo' * | tee todo.out
grep -iIlr --color --exclude="todo.sh" 'todo' *

echo -e "\n\e[1;36mTODO Finder: \e[0;36mDetails\e[0m"
#grep -iInr --color --exclude="todo.sh" 'todo' * | tee -a todo.out
grep -iInr --color --exclude="todo.sh" 'todo' *


#echo -e "\n\e[1;36mManual Finder: \e[0;36mSummary\e[0m"
##grep -iIcr --color --exclude="todo.sh" 'manual' * | tee -a todo.out
#grep -iIcr --color --exclude="todo.sh" 'manual' *

#echo -e "\n\e[1;36mManual Finder: \e[0;36mDetails\e[0m"
##grep -iInr --color --exclude="todo.sh" 'manual' * | tee -a todo.out
#grep -iInr --color --exclude="todo.sh" 'manual' *

echo

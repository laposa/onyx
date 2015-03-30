#/bin/bash -e
# Norbert @ Laposa Ltd, 2013
# Hugo @ Laposa Ltd, 2014

# fill in the below variables as per your project or a common practice
USER=www-data
GROUP=www-data

# files to be
#  - readable by all, 
#  - writeable by owner and group
#  - executable by none
MODE_FILES=0664

# directories to be
#  - readable by all,
#  - writeable by owner and group,
#  - recurseable by all and 
#  - also set the setgid bit to make sure newly
#    created files inherit parent directory group
MODE_DIRECTORIES=2775

# directories to be updated
FILES_ALL=".git bin conf controllers docs models public_html _resources templates"
PROJECT_DIR=`dirname $0`

echo "Changing current working directory to $PROJECT_DIR"
cd $PROJECT_DIR

echo "Changing owner to $USER:$GROUP on $FILES_ALL"
sudo chown $USER:$GROUP -R $FILES_ALL

for i in $FILES_ALL
    do
        echo "Fixing permissions in $i"
        find $i -type f -exec sudo chmod $MODE_FILES {} \;
        find $i -type d -exec sudo chmod $MODE_DIRECTORIES {} \;
    done

echo "Making var directory world writeable"
sudo chmod a+w -R var/

echo "Making files in bin directory executable"
sudo chmod a+x -R bin/*


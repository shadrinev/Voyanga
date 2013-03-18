shopt -s extglob
declare -A access
access=();

cutoff=$(date +%s -d "1 week ago");

packages=$(pacman -Ql | tail -n 5000);
IFS=$'\n'
for package in $packages; do
    pname=$(echo $package | cut -f1 -d' ');
    pfile=$(echo $package | cut -f2 -d' ');

    if [ ! -f $pfile ]; then
        continue;
    fi
    
    if [[ ${access[$pname]} -gt $cutoff ]]; then
        continue;
    fi;

    access_date=$(stat --format="%X" $pfile);

    if [[ $access_date -gt ${access[$pname]} ]]; then
         access[$pname]=$access_date;
    fi;

done;

for I in ${!access[*]}; do
    if [[ ${access[$I]} -lt $cutoff ]]; then
        echo $I ${access[$I]};
    fi;
done |
sort -k2 | 
gawk '{ printf "%-20s %s\n", $1, strftime("%x %T", $2) }';


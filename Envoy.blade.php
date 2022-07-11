@servers(['web' => 'developer@15.236.16.57 -p 60006'])

@setup
    $repository = 'git@gitlab.com:cvr-team/services/oryx/localviewer.git';

    $base_dir = "/var/www";
    $releases_dir = $base_dir.'/localviewer/releases';
    $app_dir = $base_dir.'/localviewer';

    $release = date('YmdHis');
    $new_release_dir = $releases_dir .'/'. $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    run_npm
    run_migrations
    update_symlinks
    do_artisans
    clean_old_releases
@endstory

@task('clone_repository')
    echo 'Create Directories'
    mkdir -p {{ $app_dir }}
    mkdir -p {{ $releases_dir }}

    echo 'Cloning repository'
    [ -d {{ $releases_dir }} ] || mkdir {{ $releases_dir }}
    git clone --single-branch -b {{ $branch }} --depth 1 {{ $repository }} {{ $new_release_dir }}
    echo 'repository cloned'
    cd {{ $new_release_dir }}
    pwd
    echo 'try to be on commit {{ $commit }}'
    git reset --hard {{ $commit }}
@endtask

@task('run_composer')
    echo "Starting deployment Backend ({{ $release }})"
    cd {{ $new_release_dir }}
    composer --no-dev install --prefer-dist --no-scripts -o
@endtask
@task('run_npm')
    echo "Starting deployment Frontend ({{ $release }})"
    cd {{ $new_release_dir }}
    npm install --silent --no-progress
    npm run production --silent --no-progress
    rm -rf node_modules
@endtask

@task('run_migrations')
    echo 'Linking .env file'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env

    cd {{ $new_release_dir }}

    echo 'Refresh Config Cache'
    php artisan config:cache
    echo "Migrating db"
    php artisan migrate --force
@endtask

@task('do_artisans')
    cd {{ $new_release_dir }}

    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    php artisan config:clear
    php artisan clear-compiled
    php artisan config:cache
    php artisan view:cache
    php artisan route:cache
    php artisan storage:link
    php artisan queue:restart

    echo "Changing permissions directory"
    sudo chgrp -R www-data .
    echo "permissions changed"
@endtask


@task('update_symlinks')
    echo "Linking storage directory"
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage

    echo 'Linking current release'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask

@task('clean_old_releases')
    # This lists our releases by modification time and delete all but the 3 most recent.
    purging=$(ls -dt {{ $releases_dir }}/* | tail -n +4);

    if [ "$purging" != "" ]; then
    echo Purging old releases: $purging;
    rm -rf $purging;
    else
    echo "No releases found for purging at this time";
    fi
@endtask

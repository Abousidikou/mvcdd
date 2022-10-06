@servers(['web' => 'emes@emes.bj -p 40000'])


@setup
    $dir = "/home/emes/sysdpp/deploy";
    $release = $dir."/releases/".date('YmdHis');
    $shared = $dir."/shared";
    $repo = $dir."/repo";
    $currentRelease = $dir."/current";
    $releaseNumber = 3;
    $dirLinks = ["tmp/cache/models","tmp/cache/persistent","tmp/cache/views","tmp/sessions","tests","logs"];
    $fileLinks = ["config/app.php","config/database.php",".env"];
@endsetup

@macro('deploy')
    createRelease
    composer
    links
    generateKey
    pclear
    currentRelease
@endmacro

@task('prepare')
    mkdir -p {{ $shared }};
    @if($remote)
        git clone {{ $remote }} {{ $repo }}
    @else
        mkdir -p {{ $repo }};
        cd {{ $repo }};
        git init --bare;
    @endif
@endtask


@task('createRelease')
    mkdir -p {{ $release }};
    cd {{ $repo }};
    @if($remote)
        git remote update;
    @endif
    git archive main | tar -x -C {{ $release }};
    echo  "Release {{ $release }} created";
@endtask

@task('composer')
    mkdir -p {{ $shared }}/vendor;
    ln -s -f {{ $shared }}/vendor {{ $release }}/vendor;
    cd {{ $release }};
composer config --no-plugins allow-plugins.symfony/thanks true;
composer clearcache;
   composer install;
    composer update --no-dev --no-progress ;{{-- --ignore-platform-reqs --}}
@endtask

@task('links')
    cd {{ $dir }}
    {{-- touch {{ $release }}/{{ ".env" }} --}}
    @foreach($dirLinks as $link)
        mkdir -p {{ $shared }}/{{ $link }};
        @if(strpos($link,'/'))
            mkdir -p {{ $release }}/{{ dirname($link) }};
        @endif 
        chmod 777 {{ $shared }}/{{ $link }};
        ln -f -s {{ $shared }}/{{ $link }} {{ $release }}/{{ $link }}; 
    @endforeach

    @foreach($fileLinks as $link)
        ln -s -f {{ $dir }}/{{ $link }} {{ $release }}/{{ $link }}; 
    @endforeach
    echo "Link created";
@endtask

@task('rollback')
    rm -f {{ $currentRelease }};
    cd {{ $release }}
    ls {{ $dir }}/releases | tail -n 2 | head -n 1 | xargs -I{} -r ln -s -f releases/{} {{ $currentRelease }};
@endtask

@task('createMigration')
    cd {{ $release }};
    php artisan migrate --path=database/migrations/deployv2 --force;
@endtask

@task('generateKey')
    cd {{ $release }};
    php artisan key:generate --force;
@endtask

@task('currentRelease')
    rm -f {{ $currentRelease }};
    chmod -R 777 {{ $release }}/public;
    chmod -R 777 {{ $release }}/storage;
    ln -s -f {{ $release }} {{ $currentRelease }};
    ls {{ $dir }}/releases | sort -r | tail -n +{{ $releaseNumber+1 }} | xargs -I{} -r rm -rf {{ $dir }}/releases/{};
    echo "Link {{ $currentRelease }} --> {{ $release }} created";
@endtask

@task('pclear')
    cd {{ $release }};
    php artisan cache:clear;
    php artisan view:clear;
    php artisan config:clear;
    php artisan route:clear;
@endtask
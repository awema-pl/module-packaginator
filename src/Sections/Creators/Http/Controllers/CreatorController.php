<?php

namespace AwemaPL\Packaginator\Sections\Creators\Http\Controllers;

use AwemaPL\Auth\Controllers\Traits\RedirectsTo;
use AwemaPL\Packaginator\Sections\Creators\Http\Requests\StoreCreate;
use AwemaPL\Packaginator\Sections\Creators\Repositories\Contracts\HistoryRepository;
use AwemaPL\Packaginator\Sections\Creators\Resources\EloquentHistory;
use AwemaPL\Packaginator\Sections\Creators\Services\PackageCreatorService;
use AwemaPL\Packaginator\Sections\Creators\Services\PackageNameService;
use AwemaPL\Packaginator\Sections\Installations\Http\Requests\StoreInstallation;
use AwemaPL\Permission\Repositories\Contracts\PermissionRepository;
use AwemaPL\Permission\Resources\EloquentPermission;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreatorController extends Controller
{

    /**
     * Histories repository instance
     *
     * @var HistoryRepository
     */
    protected $histories;

    /** @var PackageCreatorService $packageCreator  */
    protected $packageCreator;

    /** @var PackageNameService $packageName */
    protected $packageName;

    public function __construct(HistoryRepository $histories, PackageCreatorService $packageCreator, PackageNameService $packageName)
    {
        $this->histories = $histories;
        $this->packageCreator = $packageCreator;
        $this->packageName = $packageName;
    }

    /**
     * Display create package form
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('packaginator::sections.creators.index');
    }

    /**
     * Request scope
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function scope(Request $request)
    {
        return EloquentHistory::collection(
            $this->histories->scope($request)
                ->isOwner()
                ->latest()->smartPaginate()
        );
    }

    /**
     * Download package
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function download($filename)
    {
        $path = 'temp/packaginator/' . $filename . '.zip';
        if (!Storage::exists($path)){
            abort(404);
        }
        session()->push('terminate-delete-files', $path);
        return Storage::download($path);
    }

    /**
     * Create package
     *
     * @param StoreCreate $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(StoreCreate $request)
    {
        $namePackage = $this->packageName->buildName($request->name_package);
        $dirTempName = $this->packageCreator->buildZipPackage($namePackage);
        $this->histories->create(['name' => $namePackage]);
        return response()->json([
            'redirectUrl' =>route('packaginator.creator.download', ['filename' => $dirTempName]),
        ]);
    }
}

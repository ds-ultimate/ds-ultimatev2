<?php

namespace App\Http\Controllers\Admin;

use App\Bugreport;
use App\Changelog;
use App\News;
use App\Role;
use App\Server;
use App\User;
use App\World;
use App\Http\Controllers\Controller;
use App\Util\BasicFunctions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class APIController extends Controller
{
    public function news()
    {
        abort_unless(\Gate::allows('news_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'news_show',
            'edit' => 'news_edit',
            'delete' => 'news_delete',
        ];
        $routes = [
            'show' => 'admin.news.show',
            'edit' => 'admin.news.edit',
            'delete' => 'admin.news.destroy',
        ];
        
        $model = new News();
        return DataTables::eloquent($model->newQuery())
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->editColumn('updated_at', function ($data) {
                return $data->updated_at->isoFormat("L LT");
            })
            ->rawColumns(['content_de', 'content_en', 'actions'])
            ->toJson();
    }
    
    public function changelog()
    {
        abort_unless(\Gate::allows('changelog_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'changelog_show',
            'edit' => 'changelog_edit',
            'delete' => 'changelog_delete',
        ];
        $routes = [
            'show' => 'admin.changelogs.show',
            'edit' => 'admin.changelogs.edit',
            'delete' => 'admin.changelogs.destroy',
        ];
        
        $model = new Changelog();
        return DataTables::eloquent($model->newQuery())
            ->editColumn('repository_html_url', function($data) {
                return '<a href="'.($data->repository_html_url ?? '').'">'.($data->repository_html_url ?? '').'</a>';
            })
            ->editColumn('icon', function($data) {
                return '<h2 class="mb-0"><i class="'.($data->icon ?? '').'"></i></h2>';
            })
            ->editColumn('color', function($data) {
                return '<label class="form-check-label" for="inlineRadio1" style="width: 20px; height: 20px; background-color: '.$data->color.'"></label>';
            })
            ->editColumn('updated_at', function($data) {
                return $data->updated_at->diffForHumans();
            })
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->rawColumns(['repository_html_url', 'icon', 'color', 'updated_at', 'actions'])
            ->toJson();
    }
    
    public function roles()
    {
        abort_unless(\Gate::allows('role_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'role_show',
            'edit' => 'role_edit',
            'delete' => 'role_delete',
        ];
        $routes = [
            'show' => 'admin.roles.show',
            'edit' => 'admin.roles.edit',
            'delete' => 'admin.roles.destroy',
        ];
        
        $model = new Role();
        return DataTables::eloquent($model->newQuery())
            ->editColumn('permissions', function($data) {
                $retval = "";
                foreach($data->permissions as $elm) {
                    $retval .= "<span class='badge badge-info'>".BasicFunctions::escape($elm->title)."</span> ";
                }
                return $retval;
            })
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->rawColumns(['permissions', 'actions'])
            ->toJson();
    }
    
    public function users()
    {
        abort_unless(\Gate::allows('user_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'user_show',
            'edit' => 'user_edit',
            'delete' => 'user_delete',
        ];
        $routes = [
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'delete' => 'admin.users.destroy',
        ];
        
        $model = new User();
        return DataTables::eloquent($model->newQuery())
            ->editColumn('roles', function($data) {
                $retval = "";
                foreach($data->roles as $elm) {
                    $retval .= "<span class='badge badge-info'>".BasicFunctions::escape($elm->title)."</span> ";
                }
                return $retval;
            })
            ->editColumn('email_verified_at', function($data) {
                if($data->email_verified_at == null) return "-";
                return $data->email_verified_at->format(config('panel.date_format') . " " . config('panel.time_format'));
            })
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->rawColumns(['roles', 'actions'])
            ->toJson();
    }
    
    public function servers()
    {
        abort_unless(\Gate::allows('server_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'server_show',
            'edit' => 'server_edit',
            'delete' => 'server_delete',
        ];
        $routes = [
            'show' => 'admin.server.show',
            'edit' => 'admin.server.edit',
            'delete' => 'admin.server.destroy',
        ];
        
        $model = new Server();
        return DataTables::eloquent($model->newQuery())
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->editColumn("flag", function ($data) {
                if($data->flag == null) return "-";
                $flag = BasicFunctions::escape($data->flag);
                return "<span class='flag-icon flag-icon-{$flag}'></span> [{$flag}]";
            })
            ->editColumn("active", function ($data) {
                if($data->active == 1) {
                    return '<span class="fas fa-check" style="color: green"></span>';
                }
                return '<span class="fas fa-times" style="color: red"></span>';
            })
            ->rawColumns(['flag', 'active', 'actions'])
            ->toJson();
    }
    
    public function worlds()
    {
        abort_unless(\Gate::allows('world_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'world_show',
            'edit' => 'world_edit',
            'delete' => 'world_delete',
        ];
        $routes = [
            'show' => 'admin.worlds.show',
            'edit' => 'admin.worlds.edit',
            'delete' => 'admin.worlds.destroy',
        ];
        $now = Carbon::now();
        
        $model = new World();
        return DataTables::eloquent($model->newQuery())
            ->editColumn("server", function ($data) {
                return "<span class='flag-icon flag-icon-".BasicFunctions::escape($data->server->flag)."'></span>"
                        . " [".BasicFunctions::escape($data->server->code)."]";
            })
            ->editColumn("ally_count", function ($data) {
                return BasicFunctions::numberConv($data->ally_count);
            })
            ->editColumn("player_count", function ($data) {
                return BasicFunctions::numberConv($data->player_count);
            })
            ->editColumn("village_count", function ($data) {
                return BasicFunctions::numberConv($data->village_count);
            })
            ->editColumn("url", function ($data) {
                $url = BasicFunctions::escape($data->url);
                return "<a href='{$url}' target='_blank'>{$url}</a>";
            })
            ->editColumn("active", function ($data) {
                return BasicFunctions::worldStatus($data->active);
            })
            ->editColumn("worldUpdated_at", function ($data) use($now) {
                $dateOld = $data->active != null &&
                        $now->diffInSeconds($data->worldUpdated_at) >= ((60*60)*config('dsUltimate.db_update_every_hours'))*2;
                $class = $dateOld?" class='bg-danger'":"";
                return "<div$class>".$data->worldUpdated_at->diffForHumans()."</div>";
            })
            ->editColumn("worldCleaned_at", function ($data) use($now) {
                $dateOld = $data->active != null &&
                        $now->diffInSeconds($data->worldCleaned_at) >= ((60*60)*config('dsUltimate.db_clean_every_hours'))*2;
                $class = $dateOld?" class='bg-danger'":"";
                return "<div$class>".$data->worldCleaned_at->diffForHumans()."</div>";
            })
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->rawColumns(['server', 'url', 'worldUpdated_at', 'worldCleaned_at', 'active', 'actions'])
            ->toJson();
    }
    
    public function bugreports(Request $request)
    {
        abort_unless(\Gate::allows('bugreport_access'), 403);
        \App\Http\Controllers\API\DatatablesController::limitResults(500);
        
        $permissions = [
            'show' => 'bugreport_show',
            'edit' => 'bugreport_edit',
            'delete' => 'bugreport_delete',
        ];
        $routes = [
            'show' => 'admin.bugreports.show',
            'edit' => 'admin.bugreports.edit',
            'delete' => 'admin.bugreports.destroy',
        ];
        
        $model = new Bugreport();
        return DataTables::eloquent($model->newQuery())
            ->filter(function($query) use($request) {
                $query->where(function($query) use($request) {
                    $data = $request->get("prio", array());
                    foreach($data as $key => $value) {
                        if($value) {
                            $query = $query->orwhere('priority', $key);
                        }
                    }
                })->where(function($query) use($request) {
                    $data = $request->get("status", array());
                    foreach($data as $key => $value) {
                        if($value) {
                            $query = $query->orwhere('status', $key);
                        }
                    }
                });
            }, true)
            ->editColumn("priority", function ($data) {
                return $data->getPriorityBadge();
            })
            ->editColumn("title", function ($data) {
                $title = BasicFunctions::escape($data->title);
                if ($data->firstSeen === null) {
                    return "<b>{$title}</b><i class='badge badge-primary'>".__('admin.bugreport.new')."</i>";
                } else {
                    return $title;
                }
            })
            ->editColumn("status", function ($data) {
                return $data->getStatusBadge();
            })
            ->addColumn("comments", function ($data) {
                if($data->comments == null) return 0;
                return $data->comments->count();
            })
            ->editColumn("created_at", function ($data) {
                return $data->created_at->diffForHumans();
            })
            ->addColumn('actions', function ($data) use($permissions, $routes) {
                return $this->generateActions($permissions, $routes, $data->id);
            })
            ->rawColumns(['priority', 'title', 'status', 'actions'])
            ->toJson();
    }
    
    public function generateActions($permissions, $routes, $id) {
        $actions = "";
        if(\Gate::allows($permissions['show'])) {
            $actions.= '<a class="btn btn-xs btn-primary mx-2" href="' .
                route($routes['show'], $id) . '"><i class="far fa-eye"></i></a>';
        }
        if(\Gate::allows($permissions['edit'])) {
            $actions.= '<a class="btn btn-xs btn-info mx-2" href="' .
                route($routes['edit'], $id) . '"><i class="far fa-edit"></i></a>';
        } // global.edit / global.view / global.delete
        if(\Gate::allows($permissions['delete'])) {
            $actions.= '<form action="' . route($routes['delete'], $id) .
                '" method="POST" onsubmit="return confirm(\''.__('global.areYouSure').
                '\');" style="display: inline-block;">';
            $actions.= '<input type="hidden" name="_method" value="DELETE">';
            $actions.= '<button type="submit" class="btn btn-xs btn-danger mx-2"><i class="far fa-trash-alt"></i></button>';
            $actions.= '</form>';
        }
        return $actions;
    }
}

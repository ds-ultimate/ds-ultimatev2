<?php

namespace App\Http\Controllers\Tools;

use App\World;
use App\Util\BasicFunctions;
use App\Util\BuildingUtils;
use App\Tool\AccMgrDB\AccountManagerRating;
use App\Tool\AccMgrDB\AccountManagerTemplate;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AccMgrDB extends BaseController
{
    public function index(){
        BasicFunctions::local();
        $worldArray = [];
        return view('tools.accMgrDB.index', compact('worldArray'));
    }
    
    public function index_world($server, $world){
        BasicFunctions::local();
        World::existWorld($server, $world);

        $worldData = World::getWorld($server, $world);
        $worldArray = [$worldData->server->code, $worldData->name];
        return view('tools.accMgrDB.index', compact('worldArray'));
    }
    
    public function api($server=null, $world=null) {
        BasicFunctions::local();
        $filter = null;
        if($server !== null && $world !== null) {
            World::existWorld($server, $world);
            
            $worldData = World::getWorld($server, $world);
            $buildingConfig = simplexml_load_string($worldData->buildings);
            $unitConfig = simplexml_load_string($worldData->units);
            
            $hasWatchtower = isset($buildingConfig->watchtower);
            $hasChurch = isset($buildingConfig->church);
            $hasStatue = isset($unitConfig->knight);
            $filter = function($query) use($hasWatchtower, $hasChurch, $hasStatue) {
                if(! $hasWatchtower) {
                    $query = $query->where('contains_watchtower', false);
                }
                if(! $hasChurch) {
                    $query = $query->where('contains_church', false);
                }
                if(! $hasStatue) {
                    $query = $query->where('contains_statue', false);
                }
            };
        }
        
        $model = new AccountManagerTemplate();
        $dataTbl = DataTables::eloquent($model->newQuery());
        if($filter !== null) {
            $dataTbl = $dataTbl->filter($filter, true);
        }
        return $dataTbl->filter(function($query) {
                return $query->where(function($query) {
                    $query = $query->where('public', true);
                    if(\Auth::check()) {
                        $query = $query->orWhere('user_id', \Auth::user()->id);
                    }
                    return $query;
                });
            }, true)
            ->addColumn('actions', function ($data) {
                $actions = '<a class="btn btn-xs btn-primary mx-2" href="' .
                    route("tools.accMgrDB.show", $data->id) . '"><i class="far fa-eye"></i></a>';
                
                if(\Auth::check() && $data->user_id == \Auth::user()->id) {
                    $actions.= '<a class="btn btn-xs btn-info mx-2" href="' .
                        route("tools.accMgrDB.edit", $data->id) . '"><i class="far fa-edit"></i></a>';
                    
                    $actions.= '<form action="' . route('tools.accMgrDB.delete') .
                        '" method="POST" onsubmit="return confirm(\''.__('global.areYouSure').
                        '\');" style="display: inline-block;">';
                    $actions.= '<input type="hidden" name="_method" value="DELETE">';
                    $actions.= '<input type="hidden" name="id" value="'.$data->id.'">';
                    $actions.= csrf_field();
                    $actions.= '<button type="submit" class="btn btn-xs btn-danger mx-2"><i class="far fa-trash-alt"></i></button>';
                    $actions.= '</form>';
                }
                return $actions;
            })
            ->addColumn('type', function ($data) {
                if($data->contains_watchtower) {
                    $watch = asset("images/icons/dsimg/watchtower.png");
                } else {
                    $watch = asset("images/icons/dsimg/watchtower_no.png");
                }
                
                if($data->contains_church) {
                    $church = asset("images/icons/dsimg/church.png");
                } else {
                    $church = asset("images/icons/dsimg/church_no.png");
                }
                
                if($data->contains_statue) {
                    $statue = asset("images/icons/dsimg/statue.png");
                } else {
                    $statue = asset("images/icons/dsimg/statue_no.png");
                }
                $retval = "<img style='height: 2rem;' class='mr-1' src='$watch'> ";
                $retval.= "<img style='height: 2rem;' class='mr-1' src='$church'> ";
                $retval.= "<img style='height: 2rem;' class='mr-1' src='$statue'>";
                return $retval;
            })
            ->editColumn("user_id", function($data) {
                return $data->user->name;
            })
            ->editColumn('rating', function($data) {
                $retval = "";
                for($i = 0; $i < floor($data->rating); $i++) {
                    $retval.= '<i class="fas fa-star"></i>';
                }
                if(round($data->rating) != floor($data->rating)) {
                    $retval.= '<i class="fas fa-star-half-alt"></i>';
                }
                for($i = round($data->rating); $i < 5; $i++) {
                    $retval.= '<i class="far fa-star"></i>';
                }
                
                $retval.= " " . round($data->rating, 1);
                $retval.= " (" . $data->totalVotes . ")";
                return $retval;
            })
            ->editColumn('public', function($data) {
                if($data->public) {
                    return '<i class="far fa-eye"></i>';
                } else {
                    return '<i class="fas fa-lock"></i>';
                }
            })
            ->rawColumns(['actions', 'type', 'rating', 'public'])
            ->toJson();
    }
    
    public function create() {
        abort_unless(\Auth::check(), 403, "Please log in to create Templates");
        
        return $this->showEditForm(null);
    }
    
    public function edit(AccountManagerTemplate $template) {
        abort_unless(\Auth::check(), 403);
        abort_unless(\Auth::user()->id == $template->user_id, 403);
        
        return $this->showEditForm($template);
    }
    
    public function showEditForm($model) {
        $igno = [];
        $buildings = [];
        $result = [];
        if($model !== null) {
            $igno = $model->buildingsIgnored();
            $id = $model->id;
            $tmp = $this->convertBuldings($model->buildingArray());
            $buildings = $tmp['detailed'];
            $result = $tmp['ges'];
            $export = $model->exportDS();
            $showKey = $model->show_key;
        } else {
            $id = -1;
            $export=null;
        }
        $formEntries = [
            BasicFunctions::formEntryEdit($model, "text", __('tool.accMgrDB.name'), "name", __('tool.accMgrDB.name_def'), false, true),
            BasicFunctions::formEntryEdit($model, "check", __('tool.accMgrDB.public'), "public", false, false, false),
            BasicFunctions::formEntryEdit($model, "check", __('tool.accMgrDB.remAddit'), "remove_additional", false, false, false),
            BasicFunctions::formEntryEdit(null, "check", __('tool.accMgrDB.remChurch'), "removeIgno_church",
                    in_array(array_search("church", AccountManagerTemplate::$BUILDING_NAMES), $igno), false, false),
            BasicFunctions::formEntryEdit(null, "check", __('tool.accMgrDB.remFirstChurch'), "removeIgno_church_f",
                    in_array(array_search("church_f", AccountManagerTemplate::$BUILDING_NAMES), $igno), false, false),
            BasicFunctions::formEntryEdit(null, "check", __('tool.accMgrDB.remWT'), "removeIgno_watchtower",
                    in_array(array_search("watchtower", AccountManagerTemplate::$BUILDING_NAMES), $igno), false, false),
        ];
        
        return view('tools.accMgrDB.edit', compact('id', 'export', 'formEntries', 'buildings', 'result', 'showKey'));
    }
    
    public function show(AccountManagerTemplate $template, $key=null) {
        $isAccessAllowed = $template->public;
        $isAccessAllowed |= \Auth::check() && $template->user_id == \Auth::user()->id;
        $isAccessAllowed |= $template->show_key == $key;
        abort_unless($isAccessAllowed, 403);
        
        $igno = $template->buildingsIgnored();
        $tmp = $this->convertBuldings($template->buildingArray());
        $buildings = $tmp['detailed'];
        $result = $tmp['ges'];
        
        $ignored = [
            "church" => in_array(array_search("church", AccountManagerTemplate::$BUILDING_NAMES), $igno),
            "church_f" => in_array(array_search("church_f", AccountManagerTemplate::$BUILDING_NAMES), $igno),
            "watchtower" => in_array(array_search("watchtower", AccountManagerTemplate::$BUILDING_NAMES), $igno),
        ];
        
        $ownVote = null;
        if(\Auth::check()) {
            $ownVote = AccountManagerRating::findForUser($template->id);
        }
        
        return view('tools.accMgrDB.show', compact('template', 'ignored', 'buildings', 'result', 'ownVote', 'key'));
    }
    
    public function convertBuldings($data) {
        $retval = [];
        $result = [];
        foreach($data as $expansion) {
            $name = AccountManagerTemplate::$BUILDING_NAMES[$expansion[0]];
            if(! isset($result[$name])) {
                $result[$name] = 0;
            }
            $result[$name] += $expansion[1];
            $retval[] = [
                "name" => $name,
                "diff" => $expansion[1],
                "result" => $result[$name],
                "farm" => BuildingUtils::calculateRemainingFarm($result),
                "points" => BuildingUtils::calculatePoints($result),
            ];
        }
        return ['ges'=> $result, 'detailed' => $retval];
    }
    
    public static function createBuildingRow($name, $diff, $new, $farm, $points, $editable=true) {
        $retval = '<tr build_name="' . BasicFunctions::escape($name) . '" amount="' . BasicFunctions::escape($diff) . '">';
        if($editable) {
            $retval.= '<td class="handle" style="cursor:all-scroll">';
            $retval.= '<i class="fas fa-arrows-alt handle"></i>';
            $retval.= '</td>';
        }
        $retval.= '<td>';
        $retval.= '<img src="' . BasicFunctions::escape(BuildingUtils::getImage($name)) . '"> ';
        $retval.= __("ui.buildings." . BasicFunctions::escape($name));
        $retval.= " +" . BasicFunctions::escape($diff);
        $retval.= " (". __("tool.accMgrDB.level") . " " . BasicFunctions::escape($new);
        $retval.= ')</td>';
        $retval.= '<td class="text-right"><img src="' . BuildingUtils::getImage('farm') . '"> ' . $farm. '</td>';
        $retval.= '<td class="text-right">' . $points . ' ' . __("tool.accMgrDB.points") . '</td>';
        if($editable) {
            $retval.= '<td><a class="btn btn-danger build-remove">';
            $retval.= '<i class="far fa-trash-alt"></i>';
            $retval.= '</a></td>';
        }
        $retval.= '</tr>';
        return $retval;
    }
    
    public static function createAllBuildingsTable($result) {
        $retval = "";
        foreach(BuildingUtils::$BUILDINGS as $name=>$info) {
            $retval.= "<td><a id='building_";
            $retval.= BasicFunctions::escape($name);
            $retval.= "' class='buildHotbar font-weight-bold' style='cursor: pointer'>";
            $retval.= BasicFunctions::escape($result[$name] ?? 0);
            $retval.= "</a></td>";
        }
        return $retval;
    }
    
    public function save(Request $request) {
        abort_unless(\Auth::check(), 403);
        
        $data = $request->validate([
            'name' => 'required|string',
            'id' => 'integer',
            'buildings' => 'array|required',
            'buildings.*.build_name' => 'required|string',
            'buildings.*.amount' => 'required|integer',
            'forceSave' => 'required|integer|max:1|min:0',
            'remove_additional' => 'string',
            'removeIgno_church' => 'string',
            'removeIgno_church_f' => 'string',
            'removeIgno_watchtower' => 'string',
            'public' => 'string',
        ]);
        
        if($data['id'] == -1) {
            // we need to create a new one
            $model = new AccountManagerTemplate();
            $model->user_id = \Auth::user()->id;
            $model->show_key = Str::random(40);
        } else {
            // check if user is authorized
            $model = AccountManagerTemplate::find($data['id']);
            abort_if($model == null, 404);
            abort_unless(\Auth::user()->id == $model->user_id, 403);
        }
        
        $model->remove_additional = isset($data['remove_additional']);
        $ignored = [];
        if(isset($data['removeIgno_church'])) {
            $ignored[] = array_search("church", AccountManagerTemplate::$BUILDING_NAMES);
        }
        if(isset($data['removeIgno_church_f'])) {
            $ignored[] = array_search("church_f", AccountManagerTemplate::$BUILDING_NAMES);
        }
        if(isset($data['removeIgno_watchtower'])) {
            $ignored[] = array_search("watchtower", AccountManagerTemplate::$BUILDING_NAMES);
        }
        $model->setBuildingsIgnored($ignored);
        
        $buildings = [];
        foreach($data['buildings'] as $build) {
            $idx = array_search($build['build_name'], AccountManagerTemplate::$BUILDING_NAMES);
            abort_if($idx === false, 422, "Illegal building name given " . BasicFunctions::escape($build['build_name']));
            
            $buildings[] = [
                $idx,
                $build['amount'],
            ];
        }
        $model->setBuildings($buildings);
        $model->name = $data['name'];
        $model->public = isset($data['public']);
        $shouldSave = $data['id'] != -1 || $data['forceSave'] == 1;
        if($shouldSave) {
            $model->save();
        }
        
        //build response data
        $tmp = $this->convertBuldings($model->buildingArray());
        $convertedBuildings = $tmp['detailed'];
        $retval = "";
        foreach($convertedBuildings as $build) {
            $retval.= static::createBuildingRow($build['name'], $build['diff'], $build['result'], $build['farm'], $build['points']);
        }
        
        return response()->json([
            "saved" => $shouldSave,
            "html" => $retval,
            "table_html" => static::createAllBuildingsTable($tmp['ges']),
            "buildings" => $tmp['ges'],
            "id" => ($model->id == null)?(-1):($model->id),
            "export" => $model->exportDS(),
        ]);
    }
    
    public function import(Request $request) {
        abort_unless(\Auth::check(), 403);
        
        $data = $request->validate([
            'data' => 'required|string',
        ]);
        
        $model = AccountManagerTemplate::importDS($data['data']);
        if(gettype($model) == "string") {
            //error message
            return response()->json([
                "success" => 0,
                "error" => $model,
            ]);
        }
        $model->save();
        
        return response()->json([
            "success" => 1,
            "url" => route("tools.accMgrDB.edit", $model->id),
        ]);
    }
    
    public function delete(Request $request) {
        abort_unless(\Auth::check(), 403);
        
        $data = $request->validate([
            'id' => 'integer',
        ]);
        
        $model = AccountManagerTemplate::find($data['id']);
        abort_if($model == null, 404);
        abort_unless(\Auth::user()->id == $model->user_id, 403);
        $model->delete();
        return redirect()->route("tools.accMgrDB.index");
    }
    
    public function apiRating(Request $request, AccountManagerTemplate $template) {
        abort_unless(\Auth::check(), 403);
        
        $data = $request->validate([
            'rating' => 'integer|required|min:1|max:5',
            'key' => 'string',
        ]);
        $isAccessAllowed = $template->public;
        $isAccessAllowed |= \Auth::check() && $template->user_id == \Auth::user()->id;
        $isAccessAllowed |= isset($data['key']) && $template->show_key == $data['key'];
        abort_unless($isAccessAllowed, 403);
        
        $lastRating = AccountManagerRating::findForUser($template->id);
        if($lastRating === null) {
            $lastRating = new AccountManagerRating();
            $lastRating->template_id = $template->id;
            $lastRating->user_id = \Auth::user()->id;
            $lastRating->rating = -1;
        }
        if($lastRating->rating !== $data['rating']) {
            $lastRating->rating = $data['rating'];
            $lastRating->save();
            
            $template->calculateRating();
        }
        
        return response()->json([
            "success" => 1,
            "rating" => $template->rating,
            "totalVotes" => $template->totalVotes,
        ]);
    }
}
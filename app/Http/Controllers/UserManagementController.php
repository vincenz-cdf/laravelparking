<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Settings;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

use Google\ApiCore\ApiException;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Cloud\VideoIntelligence\V1\AnnotateVideoRequest;
use Google\Cloud\VideoIntelligence\V1\Feature;
use Google\Cloud\VideoIntelligence\V1\VideoContext;
use Google\Cloud\VideoIntelligence\V1\VideoIntelligenceServiceClient;
use Google\Protobuf\Duration;
use Google\Protobuf\Internal\ByteString;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->can('viewAny',User::class))
        {
            $search = $request['search'] ?? "";
            if($search != "")
            {
                $salaries = User::where('name','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%")
                ->paginate(10);
            }
            else
            {
                $salaries = User::orderBy('name')->paginate(10);
            }
            return view('users.index', compact('salaries'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->can('create',User::class))
        {
            return view('users.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'=>[
                'required',
                'email',
                'unique:users,email'
            ],
            'name'=> 'required',
            'prenom'=> 'required',
        ]);

        $mdp = Str::random(8);
        $salarie = new User;
        $salarie->name = $request->name;
        $salarie->prenom = $request->prenom;
        $salarie->email = $request->email;
        $salarie->admin=0;
        $salarie->password = bcrypt($mdp);
        $salarie->save();

        //Mettre en place la notification

        return redirect('users')->with('status','Le salarié a bien été ajoutée');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $currentDateTime = Carbon::now();
        $salarie=User::findOrFail($id);
        if($request->user()->can('view', $salarie))
        {
            $reservations = DB::table('reservations')
            ->select('reservations.created_at', 'libelle', 'reservations.deleted_at', 'reservations.finished_at')
            ->join('users', 'reservations.user_id','=','users.id')
            ->join('places', 'reservations.place_id','=','places.id')
            ->where('user_id', $id)
            ->orderBy('reservations.created_at', 'desc')
            ->paginate(10);

            return view('salarie.history', compact('reservations', 'currentDateTime'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *->with('salarie')
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

        $salarie = User::findOrFail($id);
        if($request->user()->can('update', $salarie))
        {
            return view('users.edit', compact('salarie'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $salarie = User::findOrFail($id);
        if($request->user()->can('update', $salarie))
        {
            $request->validate([
                'email'=>[
                    'required',
                    'email',
                ],
                'name'=> 'required',
                'prenom'=> 'required',
            ]);
            $salarie->update($request->input());
            return redirect('users')->with('status','Les informations ont bien été modifiées');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function remove(Request $request, $id)
    {
        if($request->user()->can('viewAny',User::class))
        {
            $salarie = User::findOrFail($id);
            return view('users.remove', compact('salarie'));
        }
    }

    public function delete($id)
    {
        $salarie = User::findOrFail($id);
        $salarie->delete();
        return redirect('users')->with('status','Le salarié a été supprimé');
    }

    public function reserve()
    {
        // Authenticate with Google Cloud and create a VideoIntelligenceServiceClient
        $credentials = json_decode('/path/to/key.json');

        // Set up a request to analyze the video for interesting moments
        $inputFolderPath = '/home/gard/Desktop/inputFolder';
        $outputFolderPath = '/home/gard/Desktop/outputFolder/';
        $inputFolder = Storage::files($inputFolderPath);
        error_log("FFmpeg entering.\n");

        $interestingMoments = [];

        foreach ($inputFolder as $file) {
            if (!$file->isDot() && strtolower($file->getExtension()) === 'mp4') {
                $inputFilePath = $file->getRealPath();
                $outputFilePath = $outputFolderPath . DIRECTORY_SEPARATOR . $file->getFilename();

                $request = (new AnnotateVideoRequest())
                    ->setInputContent(ByteString::readFromFile($inputFilePath))
                    ->addFeatures(Feature::SHOT_CHANGE_DETECTION)
                    ->addFeatures(Feature::EXPLICIT_CONTENT_DETECTION)
                    ->addFeatures(Feature::LABEL_DETECTION)
                    ->setVideoContext((new VideoContext())
                        ->setExplicitContentDetectionConfig((new ExplicitContentDetectionConfig()))
                        ->setShotChangeDetectionConfig((new ShotChangeDetectionConfig()))
                    );

                // Call the API asynchronously and process the response to get interesting moments
                $operationResponse = $client->annotateVideoAsync($request);
                $operationResponse->pollUntilComplete();

                if ($operationResponse->operationSucceeded()) {
                    error_log("FFmpeg process exited successfully.\n");
                    $response = $operationResponse->getResult();
                    $results = $response->getAnnotationResults()[0];

                    foreach ($results->getShotAnnotations() as $segment) {
                        $startTime = $segment->getStartTimeOffset();
                        $endTime = $segment->getEndTimeOffset();
                        $duration = ($endTime->getSeconds() - $startTime->getSeconds()) + ($endTime->getNanos() - $startTime->getNanos()) / 1e9;

                        if ($duration < 11.0) {
                            $start = $segment->getStartTimeOffset();
                            $end = $segment->getEndTimeOffset();
                            $interestingMoments[] = $start->getSeconds() . "-" . $end->getSeconds();
                        }
                    }
                }

                // Build the FFmpeg command
                $commandBuilder = [];
                $commandBuilder[] = "ffmpeg -i " . escapeshellarg($inputFilePath) . " -filter_complex ";
                foreach ($interestingMoments as $i => $time) {
                    $timeArr = explode("-", $time);
                    $commandBuilder[] = "[0:v]trim=" . $timeArr[0] . ":" . $timeArr[1] . ",setpts=PTS-STARTPTS[v" . $i . "];[0:a]atrim=" . $timeArr[0] . ":" . $timeArr[1] . ",asetpts=PTS-STARTPTS[a" . $i . "];";
                }

                $commandBuilder[] = "[v0][a0]";

                for ($i = 1; $i < count($interestingMoments); $i++) {
                    $commandBuilder[] = "[v$i][a$i]";
                }
                
                $commandBuilder[] = "concat=n=".count($interestingMoments).":v=1:a=1[v][a]\" -map \"[v]\" -map \"[a]\" -c:v libx264 -c:a aac $outputFilePath";
                
                // Execute the FFmpeg command
                $command = implode(" ", $commandBuilder);
                error_log("Going here\n");
                error_log("Executing FFmpeg command: $command\n");
                
                if ($exitCode === 0) {
                    error_log("FFmpeg process exited successfully.\n");
                    return redirect(url()->previous());
                } else {
                    echo "FFmpeg process exited with code $exitCode. Error output: \n";
                    error_log("FFmpeg process exited with code $exitCode. Error output: \n");

                    return redirect(url()->previous());
                
                }
            }
        }
    }

    public function ser()
    {
        $currentDateTime = Carbon::now();
        $currentSetting = Settings::select('duree')->value('duree');
        $libres = Reservation::select('place_id')->where('finished_at','>',$currentDateTime)->get();
        $places = Place::select('id')->get();
        $result = $places->diffKeys($libres);

        $reservation = new Reservation;
        $reservation->user_id = Auth::user()->id;
        $reservation->place_id = $result->first()->id;
        $reservation->finished_at = Carbon::now()->addDays($currentSetting);
        $reservation->save();

        return redirect(url()->previous());
    }

    public function dereserve($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return redirect('dashboard')->with('reservation')->with('status','Action effectué');
    }
    
    public function activate($id)
    {
        $salarie = User::findOrFail($id);
        $salarie->active = 1;
        $salarie->update();

        return redirect('users')->with('salarie')
        ->with('status', 'Le compte a été validé');
    }

    public function setduree(Request $request)
    {
        $duree = Settings::select('duree')->value('duree');
        if($request->user()->can('viewAny',User::class))
        {
            return view('users.setduree', compact('duree'));
        }
    }

    public function updateduree(Request $request)
    {
        $modif = $request['modif'];
        $setting = Settings::firstWhere('id', 1);
        $setting->duree = $modif;
        $setting->update();
        return redirect('users')
        ->with('status', 'Réglage effectué !');
    }
}

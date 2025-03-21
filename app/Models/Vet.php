<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use App\Mail\VetCredentialsMail;



class Vet extends Model
{
    use HasFactory, SoftDeletes;
   
    protected $fillable=[
        'profile_picture',
        'title',
        'category',
        'surname',
        'given_name',
        'date_of_birth',
        'gender',
        'education',
        'marital_status',
        'group_or_practice',
        'registration_number',
        'registration_date',
        'brief_profile',
        'physical_address',
        'email',
        'primary_phone_number',
        'secondary_phone_number',
        'postal_address',
        'services_offered',
        'areas_of_operation',
        'certificate_of_registration',
        'license',
        'other_documents',
        'status',
        'user_id',
        'added_by'
    ];


    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Check if it's the admin creating the form or LDF
            $user = auth('admin')->user();
            
            if (!$user) {
                return;  // Exit early if no user
            }
        
            if ($user->inRoles(['administrator', 'ldf_admin'])) {
                // Check if the user with the same email exists
                $existingUser = User::where('email', $model->email)
                    ->orWhere('username', $model->email)
                    ->first();
                
                if (!$existingUser) {
                    // Create a new user and assign the user_id to the vet
                    $new_user = new User();
                    $new_user->username = $model->email;
                    $new_user->name = $model->surname . ' ' . $model->given_name;
                    $new_user->email = $model->email;
                    $new_user->password = bcrypt('password');
                    $new_user->avatar = $model->profile_picture ? $model->profile_picture : 'images/default_image.png';
                    $new_user->save();
        
                    $model->user_id = $new_user->id;

                }
            }
        });

          //call back to send a notification to the user
          self::created(function ($model) 
          {
            
                $user = auth('admin')->user();
            
                if (!$user) {
                     Notification::send_notification($model, 'Vet', request()->segment(count(request()->segments())));
                    return;  // Exit early if no user
                }

                if($user->inRoles(['administrator','ldf_admin']))
                {
                    $new_user = User::where('email', $model->email)
                    ->orWhere('username', $model->email)
                    ->first();
                    $new_role = new AdminRoleUser();
                    $new_role->role_id = 3;
                    $new_role->user_id = $new_user->id;
                    $new_role->save();
                }

          });


           //callback to create a user with the vet credentials after if the status is approved 
           self::updating(function ($model){
            if($model->status == 'approved'){
                $user = User::where('email', $model->email) 
                ->orWhere('username', $model->email)
                ->first();
                if(!$user){
                   //create a new user and assign the user_id to the vet
                    $new_user = new User();
                    $new_user->username = $model->email;
                    $new_user->name = $model->surname.' '.$model->given_name;
                    $new_user->email = $model->email;
                    $new_user->password = bcrypt('password');
                    $new_user->avatar = $model->profile_picture ? $model->profile_picture : 'assets/person.png';
                    $new_user->save();

                    
                    $model->user_id = $new_user->id;

                    
                    $credentials = [
                        'email' => $new_user->email,
                        'password' => 'password'
                    ];

                     // Send the credentials via email
                     Mail::to($new_user->email)->send(new VetCredentialsMail($credentials, $new_user));
                }
            }

            });
           

            //call back to send a notification to the user
            self::updated(function ($model) 
            {
                $user = $model->user_id;
                 if(!$user){
                     
                      Notification::update_notification($model, 'Vet', request()->segment(count(request()->segments())-1));
                    return;
                 }

                 else{
                    
                 
                  Notification::update_notification($model, 'Vet', request()->segment(count(request()->segments())-1));
                  $new_user = User::where('email', $model->email)
                ->orWhere('username', $model->email)
                ->first();
                
                if ($new_user) {
                    // Check if the user already has the role with role_id = 4
                    $existing_role = AdminRoleUser::where('user_id', $new_user->id)
                                                  ->where('role_id', 4)
                                                  ->first();
                
                    // Only assign the role if the user doesn't already have it
                    if (!$existing_role) {
                        $new_role = new AdminRoleUser();
                        $new_role->role_id = 4;
                        $new_role->user_id = $new_user->id;
                        $new_role->save();
                    }
                }

                 }
 
            });
     

    }

    public function ratings()
    {
        return $this->hasMany(ParavetRating::class, 'paravet_id');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    
    public function availableTimes()
    {
        return $this->hasMany(AvailableTime::class);
    }

    public function paravetRequests()
    {
        return $this->hasMany(ParavetRequest::class);
    }
}

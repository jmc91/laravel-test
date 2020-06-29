<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;

class UserRegistration extends Controller {
	public function postRegister(Request $request) {
		//Retrieve the name input field
		$request_city = $request->input('city');
		$request_state = $request->input('state');
		$request_description = $request->input('description');
		$request_tags = $request->input('tags');
		$request_links = $request->input('media_link');
		$request_title = $request->input('title');
		$request_date = $request->input('date');
		$links = preg_split("/,/", $request_links);
		echo 'City: ' . $request_city;
		echo '<br>';
		// //Retrieve the username input field
		// $state = $request->state;
		// echo 'State: ' . $state;
		// echo '<br>';

		// //Retrieve the password input field
		// $description = $request->description;
		// echo 'Description: ' . $description;
		$repo_owner = 'jmc91';
		$repo_name = 'laravel-test';
		$md_file_path = 'California.md';

		$username = 'jmc91';
		// TODO: get info from Laravel about who is clicking
		$commit_message = 'Approved from Laravel by user YYYY';
		$branch = 'laravel-approvals';

		$git_resp = GitHub::repo()->Contents()->Show($repo_owner, $repo_name, $md_file_path, $branch);
		$encoded_content = $git_resp['content'];
		$content = base64_decode($encoded_content);
		$sha = $git_resp['sha'];
		// var_dump($content);

		// =========== Process pulled down md file
		$updated_content = $this->addNewIncident($content, $request_city, $request_state, $request_title, $request_date, $request_description, $request_tags, $links);

		$git_write_resp = GitHub::repo()->Contents()->update($username, $repo_name, $md_file_path, $updated_content, $commit_message, $sha, $branch);
		var_dump($git_write_resp);
	}

	private function addNewIncident(string $existing_state_content, string $city, string $state_abbrev, string $title, string $date, string $description, string $tags, array $links): string{

		$clean_city = trim(strtolower($city));

		$city_md_block_pattern = '/^## +(?:(?!^## ).)*/ms';
		preg_match_all($city_md_block_pattern, $existing_state_content, $cities_matches);
		$incidents_blob = '';
		$city_name_pattern = '/^##\s+([^#]+)$/m';
		$request_city_index = 0;
		$cities = $cities_matches[0];
		foreach ($cities as $i => $city) {
			preg_match($city_name_pattern, $city, $city_name_matches);
			$city_name = trim(strtolower($city_name_matches[1]));
			if (strcmp($clean_city, $city_name) == 0) {
				$incidents_blob = $city;
				$request_city_index = $i;
				break;
			}
		}
		$current_max_id = $this->get_max_id_incident($incidents_blob);
		echo 'max id is' . $current_max_id;
		// technically we are counting the number of incidents before adding the new one
		// but since the city name is element 0 after splitting on ###, we effectively get the new count
		$id_incident = $this->build_id_incident($state_abbrev, $clean_city, $current_max_id + 1);
		// use blade template to build incident
		$new_incident = view('incident-template', ['title' => $title, 'date' => $date, 'description' => $description, 'tags' => $tags, 'links' => $links, 'id' => $id_incident]);

		$new_incidents_blob = $incidents_blob . $new_incident;
		$cities[$request_city_index] = $new_incidents_blob;
		return implode($cities);
	}

	private function build_id_incident(string $state_abbrev, string $city, int $id_number): string{
		$city_section = str_replace(' ', '', $city);
		return strtolower($state_abbrev . '-' . $city_section . '-' . strval($id_number));
	}

	// cannot just use the size of the incidents array, because sometimes incidents are merged together
	// making it so using the count would cause duplicate ids
	private function get_max_id_incident(string $incidents): int{
		$max_id = 0;
		preg_match_all('/^id: (.*)$/m', $incidents, $id_match);
		foreach ($id_match[0] as $id) {
			var_dump($id);
			$id_num = intval(preg_split('/-/', $id)[2]);
			if ($id_num > $max_id) {
				$max_id = $id_num;
			}
		}
		return $max_id;
	}
}
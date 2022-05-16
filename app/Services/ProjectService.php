<?php

namespace App\Services;

use App\Models\Project;

class ProjectService
{
    /**
     * Creates a new project object
     *
     * @param string|null $projectId
     * @return Project|null
     */
    public function createProject(string $projectId = null): ?Project
    {
        if (empty($projectId)) {
            return null;
        }

        $project = Project::whereProjectId($projectId)->first();
        if (!empty($project)) {
            return $project;
        }

        return Project::updateOrCreate([
            'project_id'    => $projectId
        ]);
    }
}

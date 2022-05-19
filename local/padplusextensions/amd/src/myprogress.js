// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Enhance detailed data (per top group and module) with summary data for each top group (platform).
 *
 * This returns a template context with all top groups. For each top group, it returns data for:
 * - summary display by status (coursesbystatus)
 * - detailed display (modules, as retrieved from backend)
 *
 * @param {object}  progress detailed progress data
 * @param {array}   STRINGS  map of i18n strings
 * @returns {object} topgroups context for local_padplusextensions/myprogress_content template
 */
 export function computeSummaryDataForTopGroups(progress, STRINGS) {
    // Handle singular or plural labels for courses
    const labelForStatus = (status, count = 1) => {
        switch (status) {
            case 'done':
                return count > 1 ? STRINGS['course-done-plural'] : STRINGS['course-done'];
            case 'inprogress':
                return count > 1 ? STRINGS['course-inprogress-plural'] : STRINGS['course-inprogress'];
            default:
                return count > 1 ? STRINGS['course-todo-plural'] : STRINGS['course-todo'];
        }
    };

    const topgroups = progress.map((topGroup) => {
        // Perform a group-by reduction over modules and courses
        const groupByStatus = topGroup.modules.reduce((groups, module) => {
            module.courses.forEach((course) => {
                groups[course.progress.status].push(course);
            });
            return groups;
        }, {done: [], inprogress: [], todo: []});

        // Now build summary context for each status group
        const coursesbystatus = Object.entries(groupByStatus).map(([status, group]) => ({
            status, // Useful for totalByStatus
            count: group.length,
            grouplabel: labelForStatus(status, group.length),
            // Also rebuild specific context for course summary (single link-image)
            courses: group.map(({fullname, viewurl, progress}) => ({
                namestatuslabel: `${fullname} - ${labelForStatus(progress.status)}`,
                viewurl,
                progress
            }))
        }));

        // Some last computation for top group display
        const topGroupTotal = coursesbystatus.reduce((total, statusGroup) => total + statusGroup.count, 0);
        const topgrouptotallabel = `${topGroupTotal} ${topGroupTotal > 1 ? STRINGS['course-plural'] : STRINGS['course-singular']}`;

        // Return top group context for display
        return {
            topgroupid: topGroup.id,
            topgroupname: topGroup.name,
            topgrouptotallabel,
            modules: topGroup.modules, // Modules context is already ok for display
            coursesbystatus
        };
    });

    return {
        topgroups
    };
}

/**
 * Compute course totals by todo|inprogress|done status from summary data.
 * See also PHP function compute_total_courses_by_status.
 *
 * @param {object} data summary data for each top group with coursesbystatus
 * @return {object} data context for local_padplusextensions/myprogress_short_summary template
 */
export function computeTotalCoursesByStatus(data) {
    const totalByStatus = data.topgroups.reduce((totals, topgroup) => {
        topgroup.coursesbystatus.forEach((statusGroup) => {
            const newTotal = totals[statusGroup.status].count + statusGroup.count;
            totals[statusGroup.status] = {
                count: newTotal,
                hasmany: newTotal > 1
            };
        });
        return totals;
    }, {done: {count: 0, hasmany: false}, inprogress: {count: 0, hasmany: false}, todo: {count: 0, hasmany: false}});

    const total = totalByStatus.done.count + totalByStatus.inprogress.count + totalByStatus.todo.count;
    totalByStatus.total = {
        count: total,
        hasmany: total > 1
    };

    return {
        totalByStatus
    };
}


export const globalMixins = {
    methods: {
        $getFormattedDate() {
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },
        $getProgramNameFromGuid(programs, program_guid) {
            let name = '';
            programs.forEach(program => {
                if (program.guid === program_guid) {
                    name = program.program_name;
                }
            });
            return name;
        },
        $getYesNo(value) {
            return value == true ? 'Yes' : 'No';
        }

    }
};

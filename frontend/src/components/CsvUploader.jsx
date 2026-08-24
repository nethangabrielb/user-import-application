import { useDropzone } from "react-dropzone";
import { useState } from "react";
import { toast } from "sonner";

export const CsvUploader = ({ onUpload }) => {
  const [selectedFile, setSelectedFile] = useState(null);

  const { getRootProps, getInputProps, isDragActive } = useDropzone({
    accept: { "text/csv": [".csv"] },
    maxFiles: 1,
    onDrop: (acceptedFiles) => {
      const file = acceptedFiles[0];
      setSelectedFile(file);
      onUpload(file);
    },
    onDropRejected: (fileRejections) => {
      fileRejections.forEach((file) => {
        file.errors.forEach((error) => {
          toast.error(
            `Error uploading file ${file.file.name}: ${error.message}`,
          );
        });
      });
    },
  });

  return (
    <div
      {...getRootProps()}
      className="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer w-full max-w-md mx-auto bg-slate-50 hover:bg-slate-100 transition-colors border-slate-300"
    >
      <input {...getInputProps()} />
      {isDragActive ? (
        <p className="text-slate-600">Drop the CSV here...</p>
      ) : (
        <p className="text-slate-600">
          {selectedFile
            ? selectedFile.name
            : "Drag a CSV file here, or click to select"}
        </p>
      )}
    </div>
  );
};

import { useState } from "react";
import { useDropzone } from "react-dropzone";
import { toast } from "sonner";
import {
  Upload,
  FileText,
  CheckCircle2,
  AlertTriangle,
  XCircle,
  ArrowRight,
  RefreshCw,
} from "lucide-react";
import { Button } from "@/components/ui/button";
import {
  Card,
  CardHeader,
  CardTitle,
  CardDescription,
  CardContent,
  CardFooter,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Table,
  TableHeader,
  TableBody,
  TableHead,
  TableRow,
  TableCell,
} from "@/components/ui/table";
import { CsvUploader } from "@/components/CsvUploader";
import { useMutation } from "@tanstack/react-query";
import { CountSummary } from "@/components/CountSummary";

function App() {
  const [file, setFile] = useState(null);
  const [previewData, setPreviewData] = useState(null);
  const [importResult, setImportResult] = useState(null);

  const previewCsvMutation = useMutation({
    mutationFn: async (file) => {
      const formData = new FormData();
      formData.append("file", file);

      const response = await fetch("http://localhost:8000/api/preview", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error("Upload failed");
      }

      return response.json();
    },
    onSuccess: (data) => {
      setPreviewData(data);
      toast.success("CSV preview loaded successfully!");
    },
    onError: (error) => {
      toast.error(`Error loading CSV preview: ${error.message}`);
      setPreviewData(null);
    },
  });

  const importCsvMutation = useMutation({
    mutationFn: async (file) => {
      const formData = new FormData();
      formData.append("file", file);

      const response = await fetch("http://localhost:8000/api/import", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) {
        throw new Error("Upload failed");
      }

      return response.json();
    },
    onSuccess: (data) => {
      setImportResult(data);
      toast.success(`Successfully imported ${data.counts.imported} users!`);
    },
    onError: (error) => {
      toast.error(`Import failed: ${error.message}`);
    },
  });

  const handleReset = () => {
    setFile(null);
    setPreviewData(null);
    setImportResult(null);
  };

  return (
    <div className="min-h-screen p-6 md:p-12">
      <div className="max-w-5xl mx-auto space-y-8 flex justify-center items-center flex-col">
        {/* Header: Title and subtitle */}
        <header className="text-center space-y-2">
          <h1 className="text-3xl font-bold tracking-tight text-slate-900">
            User Import Application
          </h1>
          <p className="text-slate-600">
            Upload and validate CSV file before importing to PostgreSQL
          </p>
        </header>

        {previewData ? (
          <div className="w-full space-y-6">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <CountSummary
                title="Total Found"
                description={previewData.counts.total}
              />
              <CountSummary
                title="Valid to Import"
                description={previewData.counts.imported}
              />
              <CountSummary
                title="Duplicates"
                description={previewData.counts.duplicates}
              />
              <CountSummary
                title="Invalid Records"
                description={previewData.counts.invalid}
              />
            </div>

            <Card>
              <CardHeader>
                <CardTitle>File Preview</CardTitle>
                <CardDescription>
                  Review valid records and errors before importing
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="max-h-96 overflow-y-auto border border-gray-300 rounded-md">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead>Surname</TableHead>
                        <TableHead>Email</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Details</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      {previewData.imported.map(
                        ([name, surname, email], index) => (
                          <TableRow key={`imported-${index}`}>
                            <TableCell>{name}</TableCell>
                            <TableCell>{surname}</TableCell>
                            <TableCell>{email}</TableCell>
                            <TableCell>
                              <Badge variant="success">Valid</Badge>
                            </TableCell>
                            <TableCell>Ready to import</TableCell>
                          </TableRow>
                        ),
                      )}
                      {previewData.duplicates.map((item, index) => (
                        <TableRow
                          key={`duplicate-${index}`}
                          className="bg-amber-50/50"
                        >
                          <TableCell>{item.data?.[0] || "-"}</TableCell>
                          <TableCell>{item.data?.[1] || "-"}</TableCell>
                          <TableCell>{item.email}</TableCell>
                          <TableCell>
                            <Badge variant="warning">Duplicate</Badge>
                          </TableCell>
                          <TableCell className="text-amber-700 text-xs">
                            Already exists in database
                          </TableCell>
                        </TableRow>
                      ))}
                      {previewData.invalid.map((item, index) => (
                        <TableRow
                          key={`invalid-${index}`}
                          className="bg-red-50/50"
                        >
                          <TableCell>{item.data?.[0] || "-"}</TableCell>
                          <TableCell>{item.data?.[1] || "-"}</TableCell>
                          <TableCell>{item.data?.[2] || "-"}</TableCell>
                          <TableCell>
                            <Badge variant="destructive">Error</Badge>
                          </TableCell>
                          <TableCell className="text-red-700 text-xs">
                            {item.errors?.join(", ")}
                          </TableCell>
                        </TableRow>
                      ))}
                    </TableBody>
                  </Table>
                </div>
              </CardContent>
              <CardFooter className="flex justify-between ">
                <Button onClick={handleReset}>Cancel</Button>
                <div className="flex gap-1">
                  <Button onClick={handleReset} variant="outline">
                    Import Another File
                  </Button>
                  <Button
                    onClick={importCsvMutation.mutate}
                    disabled={
                      previewData.counts.imported === 0 ||
                      importCsvMutation.isLoading
                    }
                  >
                    Import Valid Users
                  </Button>
                </div>
              </CardFooter>
            </Card>
          </div>
        ) : (
          <CsvUploader
            onUpload={(file) => {
              setFile(file);
              previewCsvMutation.mutate(file);
            }}
          />
        )}
      </div>
    </div>
  );
}

export default App;
